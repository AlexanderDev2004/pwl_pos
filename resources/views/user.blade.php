<!DOCTYPE html>
<html>

<head>
    <title>Data User</title>
</head>

<body>
    <h1>Data User</h1>
    <table border="1" cellpadding="2" cellspacing="0">
        <tr>
            <td>Username</td>
            <td>Nama</td>
            <td>Password</td>
            <td>Level ID</td>
            <td>ID</td>
            {{-- <td>Jumlah pengguna</td> --}}
        </tr>
        @foreach($data as $d)
            <tr>
                <td>{{ $d->user_id }}</td>
                <td>{{ $d->username }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->password }}</td>
                <td>{{ $d->level_id }}</td>
                {{-- <td>{{ $userCount }}</td> --}}
            </tr>
        @endforeach
    </table>
</body>

</html>
