@extends('layouts.app')

@section('content')
<div class="pt-24 pb-16 bg-white dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">About Pelevo</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                We're revolutionizing the podcast industry by creating a platform where listeners are rewarded for their time and attention.
            </p>
        </div>

        <!-- Mission Section -->
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Our Mission</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    At Pelevo, we believe that quality content should be accessible to everyone, and content creators should be fairly compensated. Our platform bridges this gap by creating a sustainable ecosystem where listeners earn rewards while supporting their favorite creators.
                </p>
                <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                    We're on a mission to transform passive listening into an engaging, rewarding experience that benefits both listeners and content creators.
                </p>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-teal-500 to-cyan-500 rounded-3xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Join Our Community</h3>
                    <p class="mb-6">Be part of the future of podcast listening. Download Pelevo today and start earning while you learn, laugh, and grow.</p>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-3xl font-bold">50K+</div>
                            <div class="text-sm opacity-90">Happy Users</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">4.8★</div>
                            <div class="text-sm opacity-90">App Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Our Values</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Innovation</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We constantly push boundaries to create new ways for users to engage with content and earn rewards.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Community</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We foster a supportive community where listeners and creators can connect and grow together.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Trust</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We prioritize transparency and security to build lasting trust with our users and partners.
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white">Our Team</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">JD</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">John Doe</h3>
                    <p class="text-teal-600 dark:text-teal-400 mb-3">CEO & Founder</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Passionate about creating innovative solutions that benefit both users and content creators.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">JS</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Jane Smith</h3>
                    <p class="text-teal-600 dark:text-teal-400 mb-3">CTO</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Leading our technical vision and ensuring our platform delivers the best user experience.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">MJ</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Mike Johnson</h3>
                    <p class="text-teal-600 dark:text-teal-400 mb-3">Head of Product</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        Focused on building features that delight our users and drive engagement.
                    </p>
                </div>
            </div>
        </div>

        <!-- Story Section -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-12 mb-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Our Story</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                        Pelevo was born from a simple observation: people love podcasts, but the listening experience could be more engaging and rewarding. Our founders, avid podcast listeners themselves, saw an opportunity to create something unique.
                    </p>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                        What started as a small team working from a garage has grown into a thriving platform with thousands of users and millions of coins earned. We're just getting started on our mission to revolutionize podcast listening.
                    </p>
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">2022</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Founded</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">2023</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Launched</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">2024</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Growing</div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-teal-100 to-cyan-100 dark:from-teal-900/30 dark:to-cyan-900/30 rounded-3xl p-8">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-lg">
                                <div class="w-12 h-12 bg-teal-500 rounded-xl mb-3"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-lg">
                                <div class="w-12 h-12 bg-cyan-500 rounded-xl mb-3"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-lg">
                                <div class="w-12 h-12 bg-teal-400 rounded-xl mb-3"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-lg">
                                <div class="w-12 h-12 bg-cyan-400 rounded-xl mb-3"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="text-center">
            <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Get in Touch</h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                We'd love to hear from you! Whether you have questions, feedback, or just want to say hello, we're here to help.
            </p>
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">Email</h3>
                    <p class="text-gray-600 dark:text-gray-300">hello@pelevo.com</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">Address</h3>
                    <p class="text-gray-600 dark:text-gray-300">123 Innovation Drive<br>Tech City, TC 12345</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">Phone</h3>
                    <p class="text-gray-600 dark:text-gray-300">+1 (555) 123-4567</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 