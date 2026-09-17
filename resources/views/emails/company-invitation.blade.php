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

  <table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    style="padding:40px 16px;"
  >
    <tr>
      <td align="center">

        <table
          width="100%"
          cellpadding="0"
          cellspacing="0"
          style="
            max-width:520px;
            background:#FFFFFF;
            border-radius:20px;
            border:1px solid #E8EDF3;
            padding:36px 32px;
          "
        >

          <!-- Logo -->
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

          <!-- Icon -->
          <tr>
            <td align="center">
              <div style="
                width:60px;
                height:60px;
                border-radius:50%;
                background:#EFF6FF;
                color:#1D4ED8;
                font-size:27px;
                line-height:60px;
                text-align:center;
                margin:auto;
              ">
                👥
              </div>
            </td>
          </tr>

          <tr>
            <td style="height:20px;"></td>
          </tr>

          <!-- Title -->
          <tr>
            <td
              align="center"
              style="
                color:#111827;
                font-size:24px;
                font-weight:700;
              "
            >
              You're invited to join a company
            </td>
          </tr>

          <tr>
            <td style="height:14px;"></td>
          </tr>

          <!-- Message -->
          <tr>
            <td
              align="center"
              style="
                color:#64748B;
                font-size:14px;
                line-height:1.7;
              "
            >
              Hello
              <strong style="color:#111827;">
                {{ $invitedUserName }}
              </strong>,
              <br><br>

              <strong style="color:#111827;">
                {{ $inviterName }}
              </strong>

              has invited you to join

              <strong style="color:#111827;">
                {{ $companyName }}
              </strong>

              on TalentFlow AI.
            </td>
          </tr>

          <tr>
            <td style="height:24px;"></td>
          </tr>

          <!-- Invitation Details -->
          <tr>
            <td>
              <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                  background:#F8FAFC;
                  border:1px solid #E8EDF3;
                  border-radius:14px;
                  padding:18px;
                "
              >

                <tr>
                  <td
                    style="
                      color:#64748B;
                      font-size:12px;
                      padding-bottom:6px;
                    "
                  >
                    Company
                  </td>
                </tr>

                <tr>
                  <td
                    style="
                      color:#111827;
                      font-size:15px;
                      font-weight:700;
                      padding-bottom:16px;
                    "
                  >
                    {{ $companyName }}
                  </td>
                </tr>

                <tr>
                  <td
                    style="
                      color:#64748B;
                      font-size:12px;
                      padding-bottom:6px;
                    "
                  >
                    Invited by
                  </td>
                </tr>

                <tr>
                  <td
                    style="
                      color:#111827;
                      font-size:15px;
                      font-weight:700;
                      padding-bottom:16px;
                    "
                  >
                    {{ $inviterName }}
                  </td>
                </tr>

                <tr>
                  <td
                    style="
                      color:#64748B;
                      font-size:12px;
                      padding-bottom:6px;
                    "
                  >
                    Role
                  </td>
                </tr>

                <tr>
                  <td
                    style="
                      color:#111827;
                      font-size:15px;
                      font-weight:700;
                    "
                  >
                    HR
                  </td>
                </tr>

              </table>
            </td>
          </tr>

          <tr>
            <td style="height:28px;"></td>
          </tr>

          <!-- View Invitation Button -->
          <tr>
            <td align="center">
              <a
                href="{{ $invitationUrl }}"
                style="
                  display:inline-block;
                  padding:14px 30px;
                  background:linear-gradient(
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
                View Invitation
              </a>
            </td>
          </tr>

          <tr>
            <td style="height:22px;"></td>
          </tr>

          <!-- Expiration -->
          <tr>
            <td
              align="center"
              style="
                color:#64748B;
                font-size:12px;
                line-height:1.6;
              "
            >
              This invitation will expire in
              <strong style="color:#111827;">
                7 days
              </strong>.
            </td>
          </tr>

          <tr>
            <td style="height:20px;"></td>
          </tr>

          <!-- Security Note -->
          <tr>
            <td
              align="center"
              style="
                color:#94A3B8;
                font-size:12px;
                line-height:1.6;
              "
            >
              If you were not expecting this invitation,
              you can safely ignore this email.
            </td>
          </tr>

          <tr>
            <td style="height:28px;"></td>
          </tr>

          <!-- Divider -->
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

          <!-- Footer -->
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