<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>TalentFlow AI</title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        background: #f8fafc;
        font-family: Arial, Helvetica, sans-serif;
        color: #0f172a;
    "
>
<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="
        width: 100%;
        background: #f8fafc;
        padding: 32px 16px;
    "
>
    <tr>
        <td align="center">

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    width: 100%;
                    max-width: 620px;
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 18px;
                    overflow: hidden;
                "
            >
                <tr>
                    <td
                        style="
                            padding: 28px 32px 20px;
                            text-align: center;
                            border-bottom: 1px solid #e2e8f0;
                        "
                    >
                        <img
                            src="https://wdudxrxdmbfwpysyjbnh.supabase.co/storage/v1/object/public/branding/Logo2.png"
                            alt="TalentFlow AI"
                            width="70"
                            style="
                                display: block;
                                margin: 0 auto 12px;
                                max-width: 70px;
                            "
                        >

                        <div
                            style="
                                font-size: 20px;
                                font-weight: 700;
                            "
                        >
                            TalentFlow AI
                        </div>
                    </td>
                </tr>

                <tr>
                    <td
                        style="
                            padding: 32px;
                        "
                    >
                        @php
                            $title = match ($action) {
                                'suspended' =>
                                    'Company access temporarily disabled',

                                'reactivated' =>
                                    'Company access restored',

                                'removed' =>
                                    'You were removed from the company',

                                default =>
                                    'Company access updated',
                            };

                            $message = match ($action) {
                                'suspended' =>
                                    'Your access to the company workspace has been temporarily disabled by a company administrator. Your TalentFlow account remains active, but you cannot access this company workspace until your access is restored.',

                                'reactivated' =>
                                    'Your access to the company workspace has been restored. You can now access the company workspace again.',

                                'removed' =>
                                    'Your company membership has been removed by a company administrator. Your TalentFlow account has not been deleted, but you no longer have access to this company workspace.',

                                default =>
                                    'Your company access has been updated.',
                            };

                            $statusLabel = match ($action) {
                                'suspended' =>
                                    'Temporarily Disabled',

                                'reactivated' =>
                                    'Active',

                                'removed' =>
                                    'Removed',

                                default =>
                                    'Updated',
                            };

                            $statusColor = match ($action) {
                                'suspended' =>
                                    '#d97706',

                                'reactivated' =>
                                    '#059669',

                                'removed' =>
                                    '#dc2626',

                                default =>
                                    '#475569',
                            };

                            $statusBackground = match ($action) {
                                'suspended' =>
                                    '#fff7ed',

                                'reactivated' =>
                                    '#ecfdf5',

                                'removed' =>
                                    '#fef2f2',

                                default =>
                                    '#f1f5f9',
                            };
                        @endphp

                        <div
                            style="
                                font-size: 16px;
                                margin-bottom: 8px;
                            "
                        >
                            Hello
                            <strong>
                                {{ $recipientName }},
                            </strong>
                        </div>

                        <h1
                            style="
                                margin: 18px 0 12px;
                                font-size: 24px;
                                line-height: 1.3;
                            "
                        >
                            {{ $title }}
                        </h1>

                        <p
                            style="
                                margin: 0;
                                font-size: 15px;
                                line-height: 1.7;
                                color: #475569;
                            "
                        >
                            {{ $message }}
                        </p>

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                margin-top: 26px;
                                border: 1px solid #e2e8f0;
                                border-radius: 14px;
                                overflow: hidden;
                            "
                        >
                            <tr>
                                <td
                                    style="
                                        padding: 14px 16px;
                                        background: #f8fafc;
                                        font-size: 13px;
                                        color: #64748b;
                                        width: 34%;
                                    "
                                >
                                    Company
                                </td>

                                <td
                                    style="
                                        padding: 14px 16px;
                                        font-size: 14px;
                                        font-weight: 700;
                                    "
                                >
                                    {{ $companyName }}
                                </td>
                            </tr>

                            <tr>
                                <td
                                    style="
                                        padding: 14px 16px;
                                        background: #f8fafc;
                                        font-size: 13px;
                                        color: #64748b;
                                        border-top: 1px solid #e2e8f0;
                                    "
                                >
                                    Status
                                </td>

                                <td
                                    style="
                                        padding: 14px 16px;
                                        border-top: 1px solid #e2e8f0;
                                    "
                                >
                                    <span
                                        style="
                                            display: inline-block;
                                            padding: 6px 10px;
                                            border-radius: 999px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: {{ $statusColor }};
                                            background: {{ $statusBackground }};
                                        "
                                    >
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <p
                            style="
                                margin: 26px 0 0;
                                font-size: 13px;
                                line-height: 1.6;
                                color: #94a3b8;
                            "
                        >
                            This is an automated message from TalentFlow AI.
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>
