<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha512-b7Vh7Q1B+6sI2tG7jGk8t6ePwz5o5Yp6vIn9YQ7U2F0dZ6w7H0jGgE2s6s0cYk6s9YkQ3H0f8s8G4P7m6Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
