<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;color:#222">
  <h2 style="margin:0 0 6px">New message from your portfolio</h2>
  <p style="color:#777;margin:0 0 18px">{{ $msg->created_at->format('d M Y, H:i') }}</p>
  <table cellpadding="8" style="width:100%;border-collapse:collapse;border:1px solid #eee">
    <tr><td style="background:#fafafa;width:130px"><b>Name</b></td><td>{{ $msg->name }}</td></tr>
    <tr><td style="background:#fafafa"><b>Email</b></td><td><a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a></td></tr>
    <tr><td style="background:#fafafa"><b>Project type</b></td><td>{{ $msg->project_type ?: '—' }}</td></tr>
    <tr><td style="background:#fafafa"><b>Budget</b></td><td>{{ $msg->budget ?: '—' }}</td></tr>
  </table>
  <h3 style="margin:22px 0 8px">Message</h3>
  <div style="white-space:pre-wrap;line-height:1.6;border-left:3px solid #ff6a3d;padding-left:14px">{{ $msg->message }}</div>
  <p style="margin-top:26px"><a href="{{ route('admin.messages.show', $msg) }}" style="background:#ff6a3d;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none">Open in admin</a></p>
</div>
