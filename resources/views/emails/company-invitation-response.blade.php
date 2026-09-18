<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#F8FAFC;
    font-family:Arial, Helvetica, sans-serif;
">

@php
    $isAccepted = $status === 'accepted';

    $statusTitle = $isAccepted
        ? 'Invitation Accepted'
        : 'Invitation Declined';

    $title = $isAccepted
        ? 'Your company invitation was accepted'
        : 'Your company invitation was declined';

    $statusColor = $isAccepted
        ? '#16A34A'
        : '#DC2626';

    $iconBackground = $isAccepted
        ? '#F0FDF4'
        : '#FFF1F2';

    $icon = $isAccepted
        ? '✓'
        : '✕';
@endphp

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="background-color:#F8FAFC;"
>
    <tr>
        <td
            align="center"
            style="padding:40px 16px;"
        >

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    max-width:520px;
                    background:#FFFFFF;
                    border-radius:20px;
                    border:1px solid #E8EDF3;
                "
            >
                <tr>
                    <td
                        style="
                            padding:36px 32px;
                        "
                    >

                        <!-- Logo -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                        >
                            <tr>
                                <td align="center">
                                    <img
                                        src="https://wdudxrxdmbfwpysyjbnh.supabase.co/storage/v1/object/public/branding/Logo2.png"
                                        width="130"
                                        alt="TalentFlow AI"
                                        style="
                                            display:block;
                                            border:0;
                                            max-width:130px;
                                        "
                                    >
                                </td>
                            </tr>
                        </table>


                        <!-- Status Icon -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="margin-top:30px;"
                        >
                            <tr>
                                <td align="center">

                                    <table
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >
                                        <tr>
                                            <td
                                                align="center"
                                                valign="middle"
                                                style="
                                                    width:58px;
                                                    height:58px;
                                                    border-radius:50%;
                                                    background-color:{{ $iconBackground }};
                                                    color:{{ $statusColor }};
                                                    font-size:28px;
                                                    font-weight:bold;
                                                "
                                            >
                                                {{ $icon }}
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                        </table>


                        <!-- Title -->
                        <h1
                            style="
                                margin:22px 0 0 0;
                                text-align:center;
                                color:#0F172A;
                                font-size:24px;
                                line-height:32px;
                                font-weight:700;
                            "
                        >
                            {{ $title }}
                        </h1>


                        <!-- Greeting -->
                        <p
                            style="
                                margin:28px 0 0 0;
                                color:#475569;
                                font-size:15px;
                                line-height:24px;
                            "
                        >
                            Hi {{ $managerName }},
                        </p>


                        <!-- Message -->
                        <p
                            style="
                                margin:12px 0 0 0;
                                color:#475569;
                                font-size:15px;
                                line-height:24px;
                            "
                        >
                            <strong style="color:#0F172A;">
                                {{ $employeeName }}
                            </strong>

                            @if($isAccepted)
                                has accepted your invitation to join
                                <strong style="color:#0F172A;">
                                    {{ $companyName }}
                                </strong>
                                as HR.
                            @else
                                has declined your invitation to join
                                <strong style="color:#0F172A;">
                                    {{ $companyName }}
                                </strong>
                                as HR.
                            @endif
                        </p>


                        <!-- Details -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                margin-top:26px;
                                background:#F8FAFC;
                                border:1px solid #E8EDF3;
                                border-radius:14px;
                            "
                        >
                            <tr>
                                <td
                                    style="
                                        padding:18px 20px;
                                    "
                                >

                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >

                                        <tr>
                                            <td
                                                style="
                                                    color:#64748B;
                                                    font-size:13px;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                Employee
                                            </td>

                                            <td
                                                align="right"
                                                style="
                                                    color:#0F172A;
                                                    font-size:13px;
                                                    font-weight:600;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                {{ $employeeName }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td
                                                style="
                                                    color:#64748B;
                                                    font-size:13px;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                Company
                                            </td>

                                            <td
                                                align="right"
                                                style="
                                                    color:#0F172A;
                                                    font-size:13px;
                                                    font-weight:600;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                {{ $companyName }}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td
                                                style="
                                                    color:#64748B;
                                                    font-size:13px;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                Role
                                            </td>

                                            <td
                                                align="right"
                                                style="
                                                    color:#0F172A;
                                                    font-size:13px;
                                                    font-weight:600;
                                                    padding-bottom:12px;
                                                "
                                            >
                                                HR
                                            </td>
                                        </tr>


                                        <tr>
                                            <td
                                                style="
                                                    color:#64748B;
                                                    font-size:13px;
                                                "
                                            >
                                                Status
                                            </td>

                                            <td
                                                align="right"
                                                style="
                                                    color:{{ $statusColor }};
                                                    font-size:13px;
                                                    font-weight:700;
                                                "
                                            >
                                                {{ $statusTitle }}
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>
                        </table>


                        <!-- Information -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                margin-top:24px;
                                background:#F8FAFC;
                                border-radius:12px;
                            "
                        >
                            <tr>
                                <td
                                    style="
                                        padding:14px 16px;
                                        color:#64748B;
                                        font-size:13px;
                                        line-height:20px;
                                    "
                                >
                                    @if($isAccepted)
                                        The employee has now joined your company team on TalentFlow AI.
                                    @else
                                        No action is required. The employee was not added to your company team.
                                    @endif
                                </td>
                            </tr>
                        </table>


                        <!-- Security Note -->
                        <p
                            style="
                                margin:26px 0 0 0;
                                color:#94A3B8;
                                font-size:12px;
                                line-height:19px;
                                text-align:center;
                            "
                        >
                            This notification was sent because you created this company invitation on TalentFlow AI.
                        </p>


                        <!-- Divider -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="margin-top:28px;"
                        >
                            <tr>
                                <td
                                    style="
                                        border-top:1px solid #E8EDF3;
                                        font-size:1px;
                                        line-height:1px;
                                    "
                                >
                                    &nbsp;
                                </td>
                            </tr>
                        </table>


                        <!-- Footer -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="margin-top:24px;"
                        >
                            <tr>
                                <td
                                    align="center"
                                    style="
                                        color:#0F172A;
                                        font-size:14px;
                                        font-weight:700;
                                    "
                                >
                                    TalentFlow AI
                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding-top:6px;
                                        color:#94A3B8;
                                        font-size:12px;
                                        line-height:18px;
                                    "
                                >
                                    AI-powered interview & recruitment platform
                                </td>
                            </tr>

                            <tr>
                                <td
                                    align="center"
                                    style="
                                        padding-top:6px;
                                        color:#64748B;
                                        font-size:12px;
                                    "
                                >
                                    talant-flow.app
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>