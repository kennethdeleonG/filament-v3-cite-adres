<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- @vite('resources/css/web/app.css') --}}
</head>

<body class="bg-gradient-to-r from-gray-100 via-white to-gray-100">


    <div class="container mx-auto min-h-screen flex flex-col items-center justify-center gap-8 md:gap-12 py-4">
        <p id="loading-text" class="text-lg">
            Please wait while your download is being prepared...
        </p>
        <p id="instruction-text" class="text-lg hidden">If the download does not start automatically, please click the
            button below.</p>

        <div id="loading-spinner" class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500 hidden"></div>

        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full hidden" id="btn"
            data-link={{ $url }} data-filename={{ $filename }} data-redirect={{ $redirect }}>Download
            File</button>
    </div>

</body>
<script>
    async function downloadImage(imageSrc, nameOfDownload) {
        const response = await fetch(imageSrc);
        const blobImage = await response.blob();
        const href = URL.createObjectURL(blobImage);

        const anchorElement = document.createElement('a');
        anchorElement.href = href;
        anchorElement.download = nameOfDownload;

        document.body.appendChild(anchorElement);
        anchorElement.click();

        document.body.removeChild(anchorElement);
        window.URL.revokeObjectURL(href);
    }

    const btn = document.getElementById('btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    const loadingText = document.getElementById('loading-text');
    const instructionText = document.getElementById('instruction-text');

    btn.addEventListener('click', () => {
        var url = btn.getAttribute('data-link');
        var filename = btn.getAttribute('data-filename');
        console.log(url);

        // Show the loading spinner
        loadingSpinner.classList.remove('hidden');

        downloadImage(url, filename)
            .then(() => {
                // Hide the loading spinner when the download is complete
                loadingSpinner.classList.add('hidden');
                loadingText.classList.add('hidden');
                instructionText.classList.remove('hidden');
                btn.classList.remove('hidden');

                var redirect = btn.getAttribute('data-redirect')
                if (redirect != '') {
                    window.location = redirect;
                }

                console.log('The image has been downloaded');
            })
            .catch(err => {
                console.log('Error downloading image: ', err);
            });
    });

    btn.click();
</script>

</html>
