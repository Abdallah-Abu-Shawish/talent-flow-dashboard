<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#F8FAFC;font-family:Arial, Helvetica, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#FFFFFF;border-radius:20px;border:1px solid #E8EDF3;padding:36px 32px;">
          
          <tr>
            <td align="center">
              <img
                src="https://wdudxrxdmbfwpysyjbnh.supabase.co/storage/v1/object/public/branding/Logo2.png"
                alt="TalentFlow AI"
                width="130"
                style="display:block;max-width:130px;height:auto;"
              />
            </td>
          </tr>

          <tr><td style="height:26px;"></td></tr>

          <tr>
            <td align="center">
              <div style="width:60px;height:60px;border-radius:50%;background:#FEF2F2;color:#DC2626;font-size:27px;line-height:60px;text-align:center;margin:auto;">
                🚪
              </div>
            </td>
          </tr>

          <tr><td style="height:20px;"></td></tr>

          <tr>
            <td align="center" style="color:#111827;font-size:24px;font-weight:700;">
              A member left your company
            </td>
          </tr>

          <tr><td style="height:14px;"></td></tr>

          <tr>
            <td align="center" style="color:#64748B;font-size:14px;line-height:1.7;">
              Hello
              <strong style="color:#111827;">{{ $managerName }}</strong>,
              <br><br>
              <strong style="color:#111827;">{{ $memberName }}</strong>
              has left
              <strong style="color:#111827;">{{ $companyName }}</strong>
              on TalentFlow AI.
            </td>
          </tr>

          <tr><td style="height:24px;"></td></tr>

          <tr>
            <td>
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border:1px solid #E8EDF3;border-radius:14px;padding:18px;">
                <tr>
                  <td style="color:#64748B;font-size:12px;padding-bottom:6px;">Member</td>
                </tr>
                <tr>
                  <td style="color:#111827;font-size:15px;font-weight:700;padding-bottom:16px;">
                    {{ $memberName }}
                  </td>
                </tr>

                <tr>
                  <td style="color:#64748B;font-size:12px;padding-bottom:6px;">Email</td>
                </tr>
                <tr>
                  <td style="color:#111827;font-size:15px;font-weight:700;padding-bottom:16px;">
                    {{ $memberEmail }}
                  </td>
                </tr>

                <tr>
                  <td style="color:#64748B;font-size:12px;padding-bottom:6px;">Role</td>
                </tr>
                <tr>
                  <td style="color:#111827;font-size:15px;font-weight:700;padding-bottom:16px;">
                    {{ strtoupper(str_replace('_', ' ', $memberRole)) }}
                  </td>
                </tr>

                <tr>
                  <td style="color:#64748B;font-size:12px;padding-bottom:6px;">Company</td>
                </tr>
                <tr>
                  <td style="color:#111827;font-size:15px;font-weight:700;">
                    {{ $companyName }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr><td style="height:28px;"></td></tr>

          <tr>
            <td align="center" style="color:#94A3B8;font-size:12px;line-height:1.6;">
              This is an automatic notification from TalentFlow AI.
            </td>
          </tr>

          <tr><td style="height:28px;"></td></tr>

          <tr>
            <td style="border-top:1px solid #E8EDF3;height:1px;"></td>
          </tr>

          <tr><td style="height:20px;"></td></tr>

          <tr>
            <td align="center" style="color:#94A3B8;font-size:11px;line-height:1.6;">
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