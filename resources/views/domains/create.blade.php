@extends('layouts.panel')

@section('content')

<div class="max-w-3xl mx-auto">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Add New Domain
        </h1>
        <p class="text-gray-500 mt-1">
            Register a new domain to monitor and manage in the system.
        </p>
    </div>

    <!-- ERROR MESSAGE -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
            <strong class="block mb-2">Please fix the following errors:</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM CARD -->
    <div class="bg-white shadow-xl rounded-xl p-8">

        <form method="POST" action="{{ route('domains.store') }}" class="space-y-6">

            @csrf

            <!-- DOMAIN -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Domain Name
                </label>

                <input
                    type="text"
                    name="domain"
                    value="{{ old('domain') }}"
                    placeholder="example.com"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <p class="text-xs text-gray-500 mt-1">
                    Enter the domain you want to manage.
                </p>
            </div>

            <!-- IP -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Server IP Address
                </label>

                <input
                    type="text"
                    name="ip"
                    value="{{ old('ip') }}"
                    placeholder="192.168.1.10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <p class="text-xs text-gray-500 mt-1">
                    Optional – used to identify the server hosting this domain.
                </p>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center gap-4 pt-4">

                <button
                    type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg shadow-md">

                    Save Domain

                </button>

                <a
                    href="{{ route('domains.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-6 py-2 rounded-lg">

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

@endsection