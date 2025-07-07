@extends('layouts.app')

@section('content')
 <!-- Hero Section -->
<section class="pt-24 pb-12 gradient-bg min-h-screen flex items-center relative overflow-hidden" data-aos="fade-in">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-teal-300 rounded-full opacity-20 blur-3xl animate-pulse-slow" data-aos="zoom-in"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-300 rounded-full opacity-20 blur-3xl animate-bounce-slow" data-aos="zoom-in" data-aos-delay="200"></div>
        <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-teal-200 rounded-full opacity-30 blur-2xl animate-float" data-aos="zoom-in" data-aos-delay="400"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-white animate-fade-in" data-aos="fade-right">
                <h1 class="text-5xl lg:text-7xl font-bold mb-6 leading-tight">
                    Listen to 
                    <span class="text-yellow-300">Podcasts</span>,
                    <span class="block">Earn <span class="text-yellow-300">Coins</span></span>
                </h1>
                <p class="text-xl mb-8 text-gray-100 leading-relaxed">
                    Turn your podcast listening time into rewards. Discover amazing content while earning coins that you can redeem for real prizes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button onclick="document.getElementById('download').scrollIntoView({behavior: 'smooth'})" class="bg-white text-primary px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 animate-glow hover:scale-105">
                        Download Now
                    </button>
                    <button @click="showDemo = true" class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-primary transition-all duration-300 hover:scale-105">
                        Watch Demo
                    </button>
                </div>
                <div class="flex items-center mt-8 space-x-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300 counter" data-target="50000">0</div>
                        <div class="text-sm text-gray-200">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300 counter" data-target="1000000">0</div>
                        <div class="text-sm text-gray-200">Coins Earned</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300 counter" data-target="10000">0</div>
                        <div class="text-sm text-gray-200">Podcasts</div>
                    </div>
                </div>
            </div>
            <div class="relative animate-slide-up" data-aos="fade-left">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('public/storage/images/app-image.png') }}"
                         alt="Pelevo App Preview"
                         class="h-200 w-auto object-contain drop-shadow-2xl rounded-2xl"
                         style="max-height: 60rem;" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">Why Choose Pelevo?</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Experience the future of podcast listening with our innovative reward system that makes every minute count.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="card-hover bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 p-8 rounded-2xl border border-teal-100 dark:border-teal-800">
                <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Earn While You Listen</h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Get rewarded with coins for every minute of podcast you listen to. The longer you listen, the more you earn.
                </p>
            </div>
            <div class="card-hover bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 p-8 rounded-2xl border border-teal-100 dark:border-teal-800">
                <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Curated Content</h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Access thousands of premium podcasts across all genres, personally curated for the best listening experience.
                </p>
            </div>
            <div class="card-hover bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 p-8 rounded-2xl border border-teal-100 dark:border-teal-800">
                <div class="w-16 h-16 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Redeem Rewards</h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Exchange your earned coins for gift cards, merchandise, premium subscriptions, and exclusive content.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">How It Works</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Start earning coins in three simple steps. It's that easy!
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <div class="absolute top-12 left-1/2 transform translate-x-8 hidden md:block">
                        <svg class="w-16 h-8 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Download & Sign Up</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Download the Pelevo app and create your free account in seconds.
                </p>
            </div>
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-24 h-24 bg-gradient-to-r from-secondary to-accent rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <div class="absolute top-12 left-1/2 transform translate-x-8 hidden md:block">
                        <svg class="w-16 h-8 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Choose & Listen</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Browse our extensive library and start listening to your favorite podcasts.
                </p>
            </div>
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-accent to-green-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <span class="text-3xl font-bold text-white">3</span>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Earn & Redeem</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Watch your coins accumulate and redeem them for amazing rewards.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- App Preview Section -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">Beautiful Design, Seamless Experience</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Our intuitive interface makes it easy to discover new podcasts, track your earnings, and manage your rewards all in one place.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Offline listening with coin earning</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Smart recommendations based on your interests</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Real-time coin tracking and statistics</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">Secure wallet and withdrawal system</span>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="dark:from-teal-900/30 dark:to-cyan-900/30 rounded-3xl p-8">
                    <img src="{{ asset('public/storage/images/app-preview.png') }}"
                         alt="Pelevo App Preview"
                         class="h-80 w-auto object-contain drop-shadow-2xl rounded-2xl"
                         style="max-height: 30rem;" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">About Pelevo</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                We're revolutionizing the podcast industry by creating a platform where listeners are rewarded for their time and attention.
            </p>
        </div>
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Our Mission</h3>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    At Pelevo, we believe that quality content should be accessible to everyone, and content creators should be fairly compensated. Our platform bridges this gap by creating a sustainable ecosystem where listeners earn rewards while supporting their favorite creators.
                </p>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-6 h-6 bg-teal-500 rounded-full flex-shrink-0 mt-1"></div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Innovative Reward System</h4>
                            <p class="text-gray-600 dark:text-gray-300">First-of-its-kind platform that rewards podcast listeners</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-6 h-6 bg-teal-500 rounded-full flex-shrink-0 mt-1"></div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Quality Content</h4>
                            <p class="text-gray-600 dark:text-gray-300">Curated selection of premium podcasts across all genres</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-6 h-6 bg-teal-500 rounded-full flex-shrink-0 mt-1"></div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Community Driven</h4>
                            <p class="text-gray-600 dark:text-gray-300">Built by podcast lovers, for podcast lovers</p>
                        </div>
                    </div>
                </div>
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
    </div>
</section>

<!-- Download Section -->
<section id="download" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-gray-900 dark:text-white">Ready to Start Earning?</h2>
        <p class="text-xl text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto">
            Download Pelevo now and transform your podcast listening experience. Start earning coins today!
        </p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <button class="bg-black dark:bg-white text-white dark:text-black px-8 py-4 rounded-full font-semibold hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-300 flex items-center space-x-3">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                <div class="text-left">
                    <div class="text-xs">Download on the</div>
                    <div class="text-sm font-semibold">App Store</div>
                </div>
            </button>
            <button class="bg-black dark:bg-white text-white dark:text-black px-8 py-4 rounded-full font-semibold hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-300 flex items-center space-x-3">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.61 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                    </svg>
                <div class="text-left">
                    <div class="text-xs">Get it on</div>
                    <div class="text-sm font-semibold">Google Play</div>
                </div>
            </button>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
            Available for iOS and Android devices
        </p>
    </div>
</section>


@endsection

<script>
// Animated Counter
function animateCounters() {
    document.querySelectorAll('.counter').forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText.replace(/,/g, '');
            const increment = Math.max(1, Math.floor(target / 100));
            if (count < target) {
                counter.innerText = (count + increment).toLocaleString();
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target.toLocaleString();
            }
        };
        updateCount();
    });
}
let countersAnimated = false;
window.addEventListener('scroll', function() {
    if (!countersAnimated) {
        const counters = document.querySelectorAll('.counter');
        if (counters.length && counters[0].getBoundingClientRect().top < window.innerHeight) {
            animateCounters();
            countersAnimated = true;
        }
    }
});
</script>