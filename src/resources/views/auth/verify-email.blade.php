<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>メール認証が必要です</title>
</head>
<body>
    <h1>メール認証の確認</h1>

    <p>
        ご登録ありがとうございます。<br>
        登録を完了するには、メールアドレスの認証が必要です。
    </p>

    <p>
        登録時に入力したメールアドレス宛に確認リンクを送信しました。<br>
        メールをご確認の上、リンクをクリックして認証を完了してください。
    </p>

    @if (session('status') === 'verification-link-sent')
        <p style="color: green;">
            新しい認証リンクを送信しました。
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">認証メールを再送信</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</body>
</html>
