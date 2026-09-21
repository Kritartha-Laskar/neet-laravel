<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Temporarily Unavailable</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.1);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-red: #ef4444;
            --notice-bg: rgba(245, 158, 11, 0.15);
            --notice-border: rgba(245, 158, 11, 0.3);
            --notice-text: #fbbf24;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #090d16;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(239, 68, 68, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(245, 158, 11, 0.05) 0%, transparent 40%);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 560px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            padding: 48px 40px;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-wrapper {
            width: 72px;
            height: 72px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: var(--accent-red);
        }

        .icon-wrapper svg {
            width: 36px;
            height: 36px;
        }

        h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 14px;
        }

        p.description {
            color: var(--text-secondary);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .notice {
            margin: 24px 0;
            padding: 16px 20px;
            background: var(--notice-bg);
            border: 1px solid var(--notice-border);
            border-radius: 12px;
            color: var(--notice-text);
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .notice svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        p.apology {
            color: #64748b;
            font-size: 14px;
            margin-top: 20px;
        }

        /* Light mode fallback styling if browser prefers light */
        @media (prefers-color-scheme: light) {
            :root {
                --card-bg: rgba(255, 255, 255, 0.95);
                --card-border: rgba(0, 0, 0, 0.08);
                --text-primary: #1e293b;
                --text-secondary: #475569;
                --notice-bg: #fffbeb;
                --notice-border: #fde68a;
                --notice-text: #b45309;
            }
            body {
                background: #f8fafc;
                background-image: 
                    radial-gradient(circle at 20% 20%, rgba(239, 68, 68, 0.04) 0%, transparent 40%),
                    radial-gradient(circle at 80% 80%, rgba(245, 158, 11, 0.03) 0%, transparent 40%);
            }
            .container {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            }
            h1 {
                color: #0f172a;
            }
            p.apology {
                color: #94a3b8;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="icon-wrapper">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
    </div>

    <h1>Service Temporarily Unavailable</h1>

    <p class="description">
        Our server subscription is currently overdue.<br>
        The service has been temporarily suspended for maintenance.
    </p>

    <div class="notice">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <span>Please contact the administrator to renew the server subscription.</span>
    </div>

    <p class="apology">
        We apologize for the inconvenience.
    </p>
</div>

</body>
</html>
