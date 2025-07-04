@extends('layouts.app')

@section('content')
<div class="pt-24 pb-16 bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">GDPR Compliance</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">Last updated: {{ date('F j, Y') }}</p>
        </div>
        <div class="prose prose-lg dark:prose-invert max-w-none">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Your Rights Under GDPR</h2>
                <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                    <li>Right to access your personal data</li>
                    <li>Right to rectify inaccurate or incomplete data</li>
                    <li>Right to erasure ("right to be forgotten")</li>
                    <li>Right to restrict or object to processing</li>
                    <li>Right to data portability</li>
                    <li>Right to withdraw consent at any time</li>
                </ul>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">How We Process Your Data</h2>
                <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                    <li>We collect and process your data only with your consent or as required by law.</li>
                    <li>Your data is used to provide, improve, and secure our services.</li>
                    <li>We do not sell your personal data to third parties.</li>
                    <li>We implement strong security measures to protect your data.</li>
                </ul>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 mb-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Consent and Control</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    By using Pelevo, you consent to our processing of your personal data as described in our Privacy Policy. You can withdraw your consent or update your preferences at any time by contacting us.
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Contact Us</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    If you have any questions about your rights or our GDPR compliance, please contact us:
                </p>
                <div class="space-y-2 text-gray-600 dark:text-gray-300">
                    <p><strong>Email:</strong> <a href="mailto:support@podemeralds.com" class="underline">support@podemeralds.com</a></p>
                    <p><strong>Website:</strong> <a href="https://www.pelevo.com" class="underline" target="_blank">www.pelevo.com</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 