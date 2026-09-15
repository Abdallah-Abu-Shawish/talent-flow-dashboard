<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#F8FAFC;
    font-family:Arial, Helvetica, sans-serif;
">

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    role="presentation"
    style="padding:40px 16px;"
>
    <tr>
        <td align="center">

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                role="presentation"
                style="
                    max-width:520px;
                    background:#FFFFFF;
                    border-radius:20px;
                    border:1px solid #E8EDF3;
                    padding:36px 32px;
                "
            >

                {{-- ================================================= --}}
                {{-- LOGO --}}
                {{-- ================================================= --}}

                <tr>
                    <td align="center">

                        <img
                            src="https://wdudxrxdmbfwpysyjbnh.supabase.co/storage/v1/object/public/branding/Logo2.png"
                            alt="TalentFlow AI"
                            width="130"
                            style="
                                display:block;
                                max-width:130px;
                                height:auto;
                            "
                        />

                    </td>
                </tr>


                <tr>
                    <td style="height:26px;"></td>
                </tr>


                {{-- ================================================= --}}
                {{-- APPROVED --}}
                {{-- ================================================= --}}

                @if($status === 'approved')

                    <tr>
                        <td align="center">

                            <div style="
                                width:60px;
                                height:60px;
                                border-radius:50%;
                                background:#ECFDF5;
                                color:#059669;
                                font-size:28px;
                                line-height:60px;
                                text-align:center;
                                margin:auto;
                            ">
                                ✓
                            </div>

                        </td>
                    </tr>


                    <tr>
                        <td style="height:20px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#111827;
                                font-size:24px;
                                font-weight:700;
                            "
                        >
                            Company request approved
                        </td>
                    </tr>


                    <tr>
                        <td style="height:14px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#64748B;
                                font-size:14px;
                                line-height:1.7;
                            "
                        >
                            Good news!
                            <br><br>

                            Your request to create

                            <strong style="color:#111827;">
                                {{ $companyName }}
                            </strong>

                            on

                            <strong style="color:#111827;">
                                TalentFlow AI
                            </strong>

                            has been approved.
                            <br><br>

                            Your company has been created successfully,
                            and your account has been assigned as

                            <strong style="color:#059669;">
                                Company Admin
                            </strong>.
                        </td>
                    </tr>


                    <tr>
                        <td style="height:28px;"></td>
                    </tr>


                    <tr>
                        <td align="center">

                            <a
                                href="https://talant-flow.app"
                                style="
                                    display:inline-block;
                                    padding:14px 30px;
                                    background:#1D4ED8;
                                    background-image:linear-gradient(
                                        90deg,
                                        #1D4ED8,
                                        #06B6D4
                                    );
                                    color:#FFFFFF;
                                    text-decoration:none;
                                    border-radius:12px;
                                    font-size:15px;
                                    font-weight:700;
                                "
                            >
                                Open TalentFlow AI
                            </a>

                        </td>
                    </tr>


                    <tr>
                        <td style="height:28px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#94A3B8;
                                font-size:12px;
                                line-height:1.6;
                            "
                        >
                            You can now access your company workspace
                            and start managing your organization.
                        </td>
                    </tr>


                {{-- ================================================= --}}
                {{-- REJECTED --}}
                {{-- ================================================= --}}

                @elseif($status === 'rejected')

                    <tr>
                        <td align="center">

                            <div style="
                                width:60px;
                                height:60px;
                                border-radius:50%;
                                background:#FEF2F2;
                                color:#DC2626;
                                font-size:28px;
                                line-height:60px;
                                text-align:center;
                                margin:auto;
                            ">
                                ×
                            </div>

                        </td>
                    </tr>


                    <tr>
                        <td style="height:20px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#111827;
                                font-size:24px;
                                font-weight:700;
                            "
                        >
                            Company request update
                        </td>
                    </tr>


                    <tr>
                        <td style="height:14px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#64748B;
                                font-size:14px;
                                line-height:1.7;
                            "
                        >
                            Your request to create

                            <strong style="color:#111827;">
                                {{ $companyName }}
                            </strong>

                            on

                            <strong style="color:#111827;">
                                TalentFlow AI
                            </strong>

                            has been reviewed.
                            <br><br>

                            Unfortunately, the request was not approved
                            at this time.
                        </td>
                    </tr>


                    @if(!empty($reviewNote))

                        <tr>
                            <td style="height:24px;"></td>
                        </tr>


                        <tr>
                            <td>

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    role="presentation"
                                    style="
                                        background:#FEF2F2;
                                        border:1px solid #FECACA;
                                        border-radius:12px;
                                    "
                                >

                                    <tr>
                                        <td style="
                                            padding:16px 18px;
                                        ">

                                            <div style="
                                                color:#991B1B;
                                                font-size:12px;
                                                font-weight:700;
                                                margin-bottom:7px;
                                            ">
                                                Review note
                                            </div>

                                            <div style="
                                                color:#7F1D1D;
                                                font-size:14px;
                                                line-height:1.6;
                                            ">
                                                {{ $reviewNote }}
                                            </div>

                                        </td>
                                    </tr>

                                </table>

                            </td>
                        </tr>

                    @endif


                    <tr>
                        <td style="height:28px;"></td>
                    </tr>


                    <tr>
                        <td
                            align="center"
                            style="
                                color:#94A3B8;
                                font-size:12px;
                                line-height:1.6;
                            "
                        >
                            If needed, please review the note above
                            before submitting another request in the future.
                        </td>
                    </tr>

                @endif


                <tr>
                    <td style="height:28px;"></td>
                </tr>


                {{-- ================================================= --}}
                {{-- DIVIDER --}}
                {{-- ================================================= --}}

                <tr>
                    <td
                        style="
                            border-top:1px solid #E8EDF3;
                            height:1px;
                        "
                    ></td>
                </tr>


                <tr>
                    <td style="height:20px;"></td>
                </tr>


                {{-- ================================================= --}}
                {{-- FOOTER --}}
                {{-- ================================================= --}}

                <tr>
                    <td
                        align="center"
                        style="
                            color:#94A3B8;
                            font-size:11px;
                            line-height:1.6;
                        "
                    >
                        TalentFlow AI
                        <br>
                        AI-powered interview & recruitment platform
                        <br>
                        talant-flow.app
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>