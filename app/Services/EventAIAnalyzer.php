<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventAIAnalyzer
{
    /**
     * Analyze event feedback using AI
     */
    public function analyzeFeedback(Event $event)
    {
        $feedback = $event->feedback()->with('user')->get();

        if ($feedback->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No feedback available to analyze'
            ];
        }

        // Prepare feedback data for AI analysis
        $feedbackData = $feedback->map(function ($item) {
            return [
                'rating' => $item->rating,
                'comment' => $item->comment ?? ''
            ];
        })->toArray();

        // Perform AI sentiment analysis
        $sentimentAnalysis = $this->performSentimentAnalysis($feedbackData);
        
        // Generate insights
        $insights = $this->generateInsights($event, $feedback, $sentimentAnalysis);

        // Update event with AI analysis
        $event->update([
            'ai_sentiment_summary' => $sentimentAnalysis['summary'],
            'ai_sentiment_score' => $sentimentAnalysis['score'],
            'ai_insights' => json_encode($insights),
            'ai_analyzed_at' => now()
        ]);

        return [
            'success' => true,
            'sentiment' => $sentimentAnalysis,
            'insights' => $insights
        ];
    }

    /**
     * Perform sentiment analysis on feedback
     */
    private function performSentimentAnalysis($feedbackData)
    {
        // Calculate overall sentiment
        $totalRatings = count($feedbackData);
        $averageRating = collect($feedbackData)->avg('rating');
        $sentimentScore = $averageRating / 5; // Normalize to 0-1

        // Analyze comments for sentiment
        $comments = collect($feedbackData)->pluck('comment')->filter()->values();
        
        $positiveWords = ['great', 'excellent', 'amazing', 'wonderful', 'fantastic', 'awesome', 
                         'love', 'perfect', 'best', 'good', 'helpful', 'informative', 'inspiring',
                         'enjoyable', 'engaging', 'valuable', 'impressive', 'outstanding'];
        
        $negativeWords = ['bad', 'poor', 'terrible', 'worst', 'disappointing', 'boring', 
                         'waste', 'useless', 'unclear', 'confusing', 'disorganized', 'lacking'];

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($comments as $comment) {
            $lowerComment = strtolower($comment);
            
            foreach ($positiveWords as $word) {
                if (str_contains($lowerComment, $word)) {
                    $positiveCount++;
                }
            }
            
            foreach ($negativeWords as $word) {
                if (str_contains($lowerComment, $word)) {
                    $negativeCount++;
                }
            }
        }

        // Generate summary based on sentiment
        if ($sentimentScore >= 0.8) {
            $summary = "Overwhelmingly Positive - Attendees loved this event! High praise across all feedback.";
            $emoji = "🎉";
        } elseif ($sentimentScore >= 0.6) {
            $summary = "Positive - Most attendees had a great experience with valuable takeaways.";
            $emoji = "😊";
        } elseif ($sentimentScore >= 0.4) {
            $summary = "Mixed - Attendees had varied experiences, some areas need improvement.";
            $emoji = "😐";
        } else {
            $summary = "Needs Improvement - Several attendees expressed dissatisfaction.";
            $emoji = "😟";
        }

        return [
            'score' => round($sentimentScore, 2),
            'summary' => $summary,
            'emoji' => $emoji,
            'average_rating' => round($averageRating, 1),
            'total_feedback' => $totalRatings,
            'positive_mentions' => $positiveCount,
            'negative_mentions' => $negativeCount,
            'sentiment_ratio' => [
                'positive' => $positiveCount,
                'negative' => $negativeCount,
                'neutral' => $totalRatings - ($positiveCount + $negativeCount)
            ]
        ];
    }

    /**
     * Generate actionable insights from feedback
     */
    private function generateInsights($event, $feedback, $sentimentAnalysis)
    {
        $insights = [];

        // Rating distribution
        $ratingDistribution = $feedback->groupBy('rating')->map->count();
        
        // Identify strengths and weaknesses
        $highRatings = $feedback->filter(fn($f) => $f->rating >= 4)->count();
        $lowRatings = $feedback->filter(fn($f) => $f->rating <= 2)->count();
        $totalFeedback = $feedback->count();

        // Key strengths
        if ($highRatings > $totalFeedback * 0.6) {
            $insights['strengths'] = [
                'High satisfaction rate: ' . round(($highRatings / $totalFeedback) * 100) . '% gave 4-5 stars',
                'Strong positive sentiment in attendee comments',
                'Event format and content resonated well with participants'
            ];
        } else {
            $insights['strengths'] = [
                'Some attendees found value in the event',
                'Engagement from committed participants'
            ];
        }

        // Areas for improvement
        $improvements = [];
        
        if ($lowRatings > $totalFeedback * 0.2) {
            $improvements[] = 'Significant number of attendees (' . round(($lowRatings / $totalFeedback) * 100) . '%) gave low ratings';
            $improvements[] = 'Review event structure and content delivery';
        }
        
        if ($sentimentAnalysis['negative_mentions'] > 3) {
            $improvements[] = 'Multiple negative sentiment indicators in comments';
            $improvements[] = 'Address specific concerns raised by attendees';
        }

        if (empty($improvements)) {
            $improvements[] = 'Minor refinements to enhance already positive experience';
            $improvements[] = 'Continue current successful approach';
        }

        $insights['improvements'] = $improvements;

        // Recommendations for future events
        $recommendations = [];
        
        if ($sentimentAnalysis['score'] >= 0.8) {
            $recommendations[] = 'Replicate this successful format for future events';
            $recommendations[] = 'Consider expanding scope or frequency';
            $recommendations[] = 'Use as case study for other event planning';
        } elseif ($sentimentAnalysis['score'] >= 0.6) {
            $recommendations[] = 'Maintain core elements while refining execution';
            $recommendations[] = 'Gather more specific feedback on improvement areas';
            $recommendations[] = 'Build on positive aspects that worked well';
        } else {
            $recommendations[] = 'Comprehensive review of event structure needed';
            $recommendations[] = 'Engage with attendees for detailed feedback';
            $recommendations[] = 'Consider alternative formats or approaches';
        }

        $insights['recommendations'] = $recommendations;

        // Attendance vs. Feedback engagement
        $participantCount = $event->participants()->count();
        $feedbackRate = round(($totalFeedback / max($participantCount, 1)) * 100);
        
        $insights['engagement_metrics'] = [
            'feedback_rate' => $feedbackRate . '%',
            'participants' => $participantCount,
            'feedback_received' => $totalFeedback,
            'engagement_level' => $feedbackRate >= 50 ? 'High' : ($feedbackRate >= 25 ? 'Moderate' : 'Low')
        ];

        // Success score (0-100)
        $successScore = round(
            ($sentimentAnalysis['score'] * 60) + // 60% weight on sentiment
            (($feedbackRate / 100) * 25) + // 25% weight on feedback engagement
            (($highRatings / $totalFeedback) * 15) // 15% weight on high ratings
        );

        $insights['success_score'] = min(100, $successScore);

        // Badge/Achievement level
        if ($successScore >= 90) {
            $insights['achievement'] = [
                'level' => 'Diamond',
                'badge' => '💎',
                'title' => 'Outstanding Event',
                'description' => 'Exceptional execution with overwhelming positive feedback'
            ];
        } elseif ($successScore >= 75) {
            $insights['achievement'] = [
                'level' => 'Gold',
                'badge' => '🥇',
                'title' => 'Excellent Event',
                'description' => 'High-quality event with strong attendee satisfaction'
            ];
        } elseif ($successScore >= 60) {
            $insights['achievement'] = [
                'level' => 'Silver',
                'badge' => '🥈',
                'title' => 'Good Event',
                'description' => 'Successful event with positive outcomes'
            ];
        } elseif ($successScore >= 40) {
            $insights['achievement'] = [
                'level' => 'Bronze',
                'badge' => '🥉',
                'title' => 'Satisfactory Event',
                'description' => 'Event met basic expectations'
            ];
        } else {
            $insights['achievement'] = [
                'level' => 'Participation',
                'badge' => '📋',
                'title' => 'Event Completed',
                'description' => 'Event held but needs significant improvement'
            ];
        }

        $insights['rating_distribution'] = $ratingDistribution->toArray();

        return $insights;
    }

    /**
     * Generate a detailed report summary
     */
    public function generateReportSummary(Event $event)
    {
        if (!$event->ai_analyzed_at) {
            return null;
        }

        $insights = json_decode($event->ai_insights, true);
        
        $report = "**Event Performance Report**\n\n";
        $report .= "**{$insights['achievement']['badge']} {$insights['achievement']['title']}**\n";
        $report .= "{$insights['achievement']['description']}\n\n";
        $report .= "**Success Score:** {$insights['success_score']}/100\n\n";
        $report .= "**Key Strengths:**\n";
        foreach ($insights['strengths'] as $strength) {
            $report .= "✓ {$strength}\n";
        }
        $report .= "\n**Areas for Improvement:**\n";
        foreach ($insights['improvements'] as $improvement) {
            $report .= "• {$improvement}\n";
        }
        $report .= "\n**Recommendations:**\n";
        foreach ($insights['recommendations'] as $rec) {
            $report .= "→ {$rec}\n";
        }

        return $report;
    }
}
