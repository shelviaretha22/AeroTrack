<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:24px;background:#F3F6F5;font-family:Arial,Helvetica,sans-serif;color:#365F65;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #E2EBE9;border-radius:16px;overflow:hidden;">
        <div style="background:#365F65;padding:20px 24px;color:#ffffff;">
            <p style="margin:0;font-size:12px;letter-spacing:.12em;text-transform:uppercase;opacity:.75;">AeroTrack</p>
            <h1 style="margin:6px 0 0;font-size:20px;">{{ $items->count() }} hal perlu tindakan Anda</h1>
        </div>

        <div style="padding:20px 24px;">
            <p style="margin:0 0 16px;font-size:14px;color:#527D82;">Halo {{ $user->name }}, berikut yang belum Anda tangani:</p>

            @foreach ($items as $item)
                @php
                    $warna = match ($item['level']) {
                        'penting' => '#D14B4B',
                        'sedang' => '#D6A14D',
                        default => '#527D82',
                    };
                @endphp
                <a href="{{ $item['url'] }}" style="display:block;margin-bottom:10px;padding:12px 14px;border:1px solid #E2EBE9;border-left:4px solid {{ $warna }};border-radius:10px;text-decoration:none;">
                    <span style="display:block;font-size:14px;font-weight:bold;color:#365F65;">{{ $item['judul'] }}</span>
                    <span style="display:block;margin-top:3px;font-size:12px;color:#7A9290;">{{ $item['pesan'] }}</span>
                </a>
            @endforeach

            <a href="{{ route('notifikasi.index') }}" style="display:inline-block;margin-top:10px;padding:10px 18px;background:#365F65;color:#ffffff;font-size:13px;font-weight:bold;text-decoration:none;border-radius:10px;">Buka semua notifikasi</a>
        </div>

        <div style="padding:14px 24px;border-top:1px solid #E2EBE9;font-size:11px;color:#8BA5A2;">
            Email ini dikirim karena notifikasi email aktif. Anda bisa mematikannya di
            <a href="{{ route('profile.index') }}" style="color:#527D82;">Profile &amp; Pengaturan</a>.
        </div>
    </div>
</body>
</html>
