@php
    $isAuthFlow = request()->is('login', 'logout', 'register', 'admin/login', 'admin/logout', 'admin/register');
@endphp

@if ($isAuthFlow)
    <script>window.location = @json(url('/'));</script>
@else
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body { height: 100%; margin: 0; }
        body {
            background: #0b0b0b;
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }
    </style>
</head>
<body>
    <script>
        Swal.fire({
            title: "Error {{ $status ?? 500 }}",
            text: @json($message ?? 'Ocurrió un error.'),
            icon: "error",
            confirmButtonText: "Volver",
            showCancelButton: false,
            showClass: {
                popup: `
                  animate__animated
                  animate__fadeInUp
                  animate__faster
                `
            },
            hideClass: {
                popup: `
                  animate__animated
                  animate__fadeOutDown
                  animate__faster
                `
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.history.back();
            }
        });
    </script>
</body>
</html>
@endif
