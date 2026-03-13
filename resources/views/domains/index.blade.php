@extends('layouts.panel')

@section('content')

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Domain Manager
            </h1>
            <p class="text-gray-500 mt-1">
                Manage and monitor all domains registered in the system.
            </p>
        </div>

        <a href="{{ route('domains.create') }}"
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg shadow-md">

            + Add Domain

        </a>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- DOMAIN TABLE -->
    <div class="bg-white shadow-xl rounded-xl overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100 border-b">

                <tr>

                    <th class="p-4 text-left text-sm font-semibold text-gray-600">
                        Domain
                    </th>

                    <th class="p-4 text-left text-sm font-semibold text-gray-600">
                        Server IP
                    </th>

                    <th class="p-4 text-left text-sm font-semibold text-gray-600">
                        Status
                    </th>

                    <th class="p-4 text-left text-sm font-semibold text-gray-600">
                        Created
                    </th>

                    <th class="p-4 text-left text-sm font-semibold text-gray-600">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($domains as $domain)

                <tr class="border-b hover:bg-gray-50 transition">

                    <!-- DOMAIN -->
                    <td class="p-4 font-semibold text-gray-800">
                        {{ $domain->domain }}
                    </td>

                    <!-- IP -->
                    <td class="p-4 text-gray-600">
                        {{ $domain->ip ?? '-' }}
                    </td>

                    <!-- STATUS -->
                    <td class="p-4">

                        @if($domain->status == 'active')

                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                            Active
                        </span>

                        @else

                        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full">
                            Pending
                        </span>

                        @endif

                    </td>

                    <!-- CREATED -->
                    <td class="p-4 text-gray-500 text-sm">
                        {{ $domain->created_at->format('Y-m-d H:i') }}
                    </td>

                    <!-- ACTION -->
                    <td class="p-4">

                        <form method="POST"
                        action="{{ route('domains.destroy', $domain) }}"
                        onsubmit="return confirm('Are you sure you want to delete this domain?')">

                        @csrf
                        @method('DELETE')

                        <button
                        class="text-red-500 hover:text-red-700 font-semibold">

                        Delete

                        </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400">
                        No domains found.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection