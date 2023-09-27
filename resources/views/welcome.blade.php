<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    {{-- NAVBAR --}}
    <nav class="bg-[#531717] border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <div class="flex items-center">
                <img src="{{ asset('images/cite-logo.png') }}" class="h-8 mr-3" alt="CITE logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white text-[#E5B040]">CITE
                    ADRES: A
                    Document Repository System</span>
            </div>

            <div class="flex items-center">
                <ul class="flex flex-row font-medium mt-0 mr-6 space-x-4 text-md">
                    <li>
                        <a href="/faculty/login" class="text-white dark:text-white hover:underline">Login</a>
                    </li>
                    <li>
                        <a href="/faculty/register" class="text-white dark:text-white hover:underline">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <nav class="bg-gray-600 dark:bg-gray-700">
        <div class="max-w-screen-xl px-4 py-3 mx-auto flex justify-center">
            <div class="flex items-center ">
                <ul class="flex flex-row font-medium mt-0 mr-6 space-x-8 text-md">
                    <li>
                        <a href="#" class="text-white dark:text-white hover:underline"
                            aria-current="page">Home</a>
                    </li>
                    <li>
                        <a href="#" class="text-white dark:text-white hover:underline">About</a>
                    </li>
                    <li>
                        <a href="#" class="text-white dark:text-white hover:underline">Contact</a>
                    </li>
                    <li>
                        <a href="#" class="text-white dark:text-white hover:underline">Links</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    {{-- HERO 1 --}}
    <div class="w-full h-[600px] relative">
        <div class="absolute inset-0 bg-cover bg-center  "
            style="background-image: url('{{ asset('images/home-bg.jpg') }}');">
            <div class="bg-black bg-opacity-50 w-full h-full flex justify-center items-center flex-col">
                <img src="{{ asset('images/bulsu-logo.png') }}" class="h-64 " alt="Bulsu logo" />
                <p class="text-[40px] relative z-10 font-bold text-white"><span class="text-[#E5B040]">CITE</span>
                    ADRES
                </p>
                <p class="text-md text-white font-bold">A DOCUMENT REPOSITORY SYSTEM</p>
            </div>
        </div>
    </div>

    {{-- FEATURES --}}
    <div class="w-full h-auto ">
        <h1 class="text-[40px] text-center w-full mt-2"> Features</h1>

        <div class="mx-24 flex justify-center items-start mt-4">
            <div class="w-1/2 flex justify-center ">
                <div
                    class="max-w-lg bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <img class="rounded-t-lg" src="{{ asset('images/home-bg.jpg') }}" alt="" />
                    <div class="p-5">
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                            Empowering users with full control over their document management needs, the CITE Department
                            Document Repository System offers a user-friendly CRUD (Create, Read, Update, Delete) system
                            that allows faculty members, staff, and students to effortlessly create, access, update, and
                            delete documents with ease. Whether it's generating new syllabi, reviewing research papers,
                            or making real-time edits to lesson plans, our system provides a seamless interface for
                            users to navigate through their document repository, ensuring efficient and effective
                            collaboration within the CITE Department. Say goodbye to manual document management hassles
                            and embrace the power of our CRUD system, putting you in command of your valuable documents.
                        </p>
                    </div>
                </div>
            </div>

            <div class="w-1/2 flex justify-center">
                <div
                    class="max-w-lg bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <img class="rounded-t-lg" src="{{ asset('images/home-bg.jpg') }}" alt="" />
                    <div class="p-5">
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                            Introducing the CITE ADRES: A Document Repository System, a groundbreaking capstone project
                            that revolutionizes document
                            management within the CITE Department. Our innovative system
                            offers a secure and user-friendly platform for storing, organizing,
                            and retrieving a wide range of departmental documents. With advanced features such as
                            intelligent categorization, version control, access control, collaboration tool, and robust
                            search capabilities, our system empowers faculty members, staff to efficiently manage and
                            access essential documents, fostering seamless collaboration and enhancing productivity.
                            Say goodbye to traditional document management challenges and embrace a new era of
                            streamlined information management with the CITE Department Document Repository System.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ABOUT US  --}}
    <div class="w-full relative bg-[#502929] pb-8 mt-6">
        <h1 class="text-[40px] text-center w-full mt-2 text-white"> ABOUT US</h1>
        <div class="text-center mx-24">

            <div>
                <img src="{{ asset('images/home-bg.jpg') }}" class="h-64 mx-auto mb-4" alt="Bulsu logo" />

                <p class="text-gray-300">
                    At CITE Department, we are a dedicated team of professionals committed to transforming the landscape
                    of computer and information technology education. With a passion for innovation and a focus on
                    empowering students and faculty alike, we strive to deliver cutting-edge solutions that enhance
                    learning experiences, promote collaboration, and drive excellence in our department. Our expertise
                    spans a wide range of areas, including curriculum development, research, and technology integration.
                    By leveraging our collective knowledge and leveraging emerging technologies, we are continuously
                    pushing boundaries to shape the future of computer and information technology education. With a
                    student-centric approach and a commitment to fostering an inclusive and supportive learning
                    environment, we are dedicated to nurturing the talents of our students and preparing them for
                    success in a rapidly evolving digital world.
                </p>
            </div>

        </div>
    </div>
</body>

</html>
