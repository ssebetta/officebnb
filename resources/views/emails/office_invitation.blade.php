<p>Hello,</p>

<p>{{ $invitation->inviter?->name ?? 'OfficeBnB' }} invited you to help manage the office listing: <strong>{{ $invitation->office?->title }}</strong>.</p>

<p><a href="{{ route('office-invitations.show-token', $invitation->token) }}" style="display: inline-block; padding: 10px 20px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0;">Accept Invitation</a></p>

<p>Or copy this link: {{ route('office-invitations.show-token', $invitation->token) }}</p>

<p>If you were not expecting this, you can ignore this email.</p>
