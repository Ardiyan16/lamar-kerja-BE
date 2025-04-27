<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tes Mail</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap">

</head>
<body style="background: #F6F8FD;font-family: 'Poppins', sans-serif">

    <table width="100%" style="min-height: 800px">
        <tr>
            <td colspan="3" style="height: 100px"></td>
        </tr>
        <tr>
            <td style="width: 30%;"></td>
            <td style="background-color: #fff;border-radius: 10px;padding: 15px 20px;">
                <table width="100%">
                    <tr style="height: 10px"></tr>
                    <tr>
                        <td style="width: 38%"></td>
                        <td align="center">
                            <img src="https://img.freepik.com/free-vector/welcome-word-flat-cartoon-people-characters_81522-4207.jpg" alt="" width="100%">
                        </td>
                        <td style="width: 38%"></td>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <td style="width: 25%"></td>
                        <td align="center">
                            {{-- <img src="{{ asset('assets/images/icons/welcome.png') }}" alt="" width="100%" style="filter: backdrop(10px)"> --}}
                        </td>
                        <td style="width: 25%"></td>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <td style="width: 15%"></td>
                        <td align="center">
                            <h3 style="font-size: 23px;font-weight: 600">
                                Selamat Datang di LamarKerja.com
                            </h3>
                        </td>
                        <td style="width: 15%"></td>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <td style="width: 5%"></td>
                        <td>
                            <p style="font-size: 20px">Hai, {{ $send_data['username'] }}</p>
                        </td>
                        <td style="width: 5%"></td>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <td style="width: 5%"></td>
                        <td>
                            <p style="font-size: 13px; margin: 0; color: #7F7F7F">
                                Terima kasih telah mendaftar di LamarKerja.com! Selamat bergabung di LamarKerja.com, platform terpercaya untuk menemukan peluang karier terbaik Anda. Temukan lowongan kerja impianmu sekarang!. Silahkan klik tombol dibawah ini untuk link verifikasi akun anda
                            </p>
                        </td>
                        <td style="width: 5%"></td>
                    </tr>
                </table>
                <table width="100%" style="margin-top: 30px;">
                    <tr>
                        <td style="width: 35%;"></td>
                        <td align="center">
                            <a href="{{ url($send_data['link']) }}" style="display: block;width: 100%;border-radius: 10px;background: #33B0E4;padding: 10px 15px;text-decoration: none;color: #fff;font-size: 13px;">
                                Link Verifikasi Akun
                            </a>
                        </td>
                        <td style="width: 35%;"></td>
                    </tr>
                    <tr style="height: 10px"></tr>
                </table>
                <table width="100%">
                    <tr>
                        <td style="width: 5%"></td>
                        <td>
                            <p style="font-size: 13px; margin: 0; color: #7F7F7F">
                                Selamat bergabung di LamarKerja.com untuk memudahkan anda dalam mendapatkan informasi lowongan dan lamaran kerja terbaru dan professional
                            </p>
                            <p style="font-size: 13px; margin: 0; color: #7F7F7F; margin-top: 20px">
                                Salam, Tim LamarKerja.com
                            </p>
                        </td>
                        <td style="width: 5%"></td>
                    </tr>
                </table>
            </td>
            <td style="width: 30%;"></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <p style="text-align: center;color: #7F7F7F;font-size: 14px;">Copyright {{ date('Y') }} | LamarKerja.com</p>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="height: 100px"></td>
        </tr>
    </table>

</body>
</html>