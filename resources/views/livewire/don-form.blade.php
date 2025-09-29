<form wire:submit="submit">
    <div class="mb-3">
        <label for="type" class="form-label">Donation Type</label>
        <select wire:model="type" id="type" class="form-control">
            <option value="money">Money</option>
            <option value="product">Product</option>
        </select>
        @error('type') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="number" wire:model="amount" id="amount" placeholder="Enter amount" class="form-control">
        @error('amount') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea wire:model="description" id="description" placeholder="Describe your donation (e.g., clothes, plastic)" class="form-control" rows="3"></textarea>
        @error('description') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    @if ($type === 'money')
        <div class="card mb-3" style="background-color: #f8f9fa;">
            <div class="card-header">
                <h5 class="card-title mb-0">Card Payment Simulation</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">This is a simulated payment form. No real payment will be processed.</p>
                
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" wire:model="card_number" id="card_number" placeholder="1234 5678 9012 3456" class="form-control">
                    @error('card_number') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="card_expiry" class="form-label">Expiry (MM/YY)</label>
                            <input type="text" wire:model="card_expiry" id="card_expiry" placeholder="MM/YY" class="form-control">
                            @error('card_expiry') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="card_cvv" class="form-label">CVV</label>
                            <input type="text" wire:model="card_cvv" id="card_cvv" placeholder="123" class="form-control">
                            @error('card_cvv') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" wire:model="share" class="form-check-input" id="share">
            <label class="form-check-label" for="share">
                Share this donation in the forum?
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Submit Donation</button>
</form>