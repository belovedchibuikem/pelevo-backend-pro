@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-12 p-8 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-teal-800">Contact Us</h2>
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-teal-100 text-teal-800 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
        @csrf
        <div>
            <label for="name" class="block text-gray-700 font-medium mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-400">
        </div>
        <div>
            <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-400">
        </div>
        <div>
            <label for="subject" class="block text-gray-700 font-medium mb-1">Subject</label>
            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-400">
        </div>
        <div>
            <label for="message" class="block text-gray-700 font-medium mb-1">Message</label>
            <textarea id="message" name="message" rows="5" required
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-400">{{ old('message') }}</textarea>
        </div>
        <button type="submit"
            class="w-full py-2 px-4 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded transition-colors">Send Message</button>
    </form>
</div>
@endsection 