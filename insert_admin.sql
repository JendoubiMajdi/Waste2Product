-- Insert admin user directly into the database
INSERT INTO users (name, email, email_verified_at, password, is_admin, created_at, updated_at) 
VALUES (
    'Admin User',
    'admin@example.com',
    NOW(),
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    1,
    NOW(),
    NOW()
);

-- Alternative with different password (admin12345)
-- INSERT INTO users (name, email, email_verified_at, password, is_admin, created_at, updated_at) 
-- VALUES (
--     'Admin User',
--     'admin@example.com',
--     NOW(),
--     '$2y$12$LQv3c1yqBCFcXDC.CoGXK.Sx/pJK5R/UeGE3U.bnzCVo5XtnGgduW', -- password: admin12345
--     1,
--     NOW(),
--     NOW()
-- );
