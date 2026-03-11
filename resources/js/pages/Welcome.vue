<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
  LayoutDashboard,
  PieChart as PieChartIcon,
  Tags,
  ShieldCheck,
  ArrowRight,
  Menu,
  X,
  ChevronLeft,
  ChevronRight,
} from 'lucide-vue-next'
import { ref, onMounted, onUnmounted } from 'vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { dashboard, login, register } from '@/routes'

const isMenuOpen = ref(false)
const currentSlide = ref(0)
const slideInterval = ref<ReturnType<typeof setInterval> | null>(null)

const previewImages = [
  {
    src: '/dashboard.png',
    alt: 'Smart Dashboard',
    title: 'Comprehensive Overview',
  },
  {
    src: '/transaction.png',
    alt: 'Transactions Management',
    title: 'Detailed Tracking',
  },
  {
    src: '/category.png',
    alt: 'Category Organization',
    title: 'Smart Categorization',
  },
  {
    src: '/trash.png',
    alt: 'Trash & Recovery',
    title: 'Data Protection',
  },
]

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % previewImages.length
}

const prevSlide = () => {
  currentSlide.value =
    (currentSlide.value - 1 + previewImages.length) % previewImages.length
}

const startAutoSlide = () => {
  stopAutoSlide()
  slideInterval.value = setInterval(nextSlide, 5000)
}

const stopAutoSlide = () => {
  if (slideInterval.value) {
    clearInterval(slideInterval.value)
    slideInterval.value = null
  }
}

onMounted(() => {
  startAutoSlide()
})

onUnmounted(() => {
  stopAutoSlide()
})

withDefaults(
  defineProps<{
    canRegister: boolean
  }>(),
  {
    canRegister: true,
  },
)

const features = [
  {
    title: 'Smart Dashboard',
    description:
      'Get a comprehensive overview of your financial health with real-time metrics and summaries.',
    icon: LayoutDashboard,
    color: 'text-primary',
  },
  {
    title: 'Visual Insights',
    description:
      'Understand your spending habits through beautiful, interactive area and donut charts.',
    icon: PieChartIcon,
    color: 'text-success',
  },
  {
    title: 'Flexible Categorization',
    description:
      'Organize your transactions with custom categories and colors that make sense to you.',
    icon: Tags,
    color: 'text-warning',
  },
  {
    title: 'Safe & Secure',
    description:
      'Your financial data is protected with industry-standard security and soft-delete features.',
    icon: ShieldCheck,
    color: 'text-primary',
  },
]
</script>

<template>
  <Head title="Welcome to InexTracker" />

  <div
    class="min-h-screen bg-background text-foreground selection:bg-primary selection:text-primary-foreground"
  >
    <!-- Navigation -->
    <nav
      class="sticky top-0 z-50 w-full border-b bg-background/80 backdrop-blur-md transition-all duration-300"
    >
      <div class="mx-auto flex max-w-7xl items-center justify-between p-4 px-6">
        <div class="flex items-center gap-2">
          <AppLogoIcon class="size-8 fill-current text-primary" />
          <span class="text-xl font-bold tracking-tight">InexTracker</span>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden items-center gap-8 md:flex">
          <a
            href="#features"
            class="text-sm font-medium transition-colors hover:text-primary"
            >Features</a
          >
          <a
            href="#about"
            class="text-sm font-medium transition-colors hover:text-primary"
            >About</a
          >
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden items-center gap-4 md:flex">
            <Link
              v-if="$page.props.auth.user"
              :href="dashboard()"
              class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition-all hover:bg-primary/90"
            >
              Go to Dashboard
            </Link>
            <template v-else>
              <Link
                :href="login()"
                class="px-2 text-sm font-medium transition-colors hover:text-primary"
              >
                Log in
              </Link>
              <Link
                v-if="canRegister"
                :href="register()"
                class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-sm transition-all hover:bg-primary/90"
              >
                Get Started
              </Link>
            </template>
          </div>

          <!-- Mobile Menu Toggle -->
          <button
            class="flex items-center justify-center rounded-md p-2 text-muted-foreground hover:bg-muted md:hidden"
            @click="isMenuOpen = !isMenuOpen"
          >
            <Menu v-if="!isMenuOpen" class="size-6" />
            <X v-else class="size-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div
        v-if="isMenuOpen"
        class="border-t bg-background p-6 shadow-xl transition-all duration-300 md:hidden"
      >
        <div class="flex flex-col gap-4">
          <a
            href="#features"
            class="text-lg font-medium transition-colors hover:text-primary"
            @click="isMenuOpen = false"
            >Features</a
          >
          <a
            href="#about"
            class="text-lg font-medium transition-colors hover:text-primary"
            @click="isMenuOpen = false"
            >About</a
          >
          <hr class="my-2 border-muted" />
          <Link
            v-if="$page.props.auth.user"
            :href="dashboard()"
            class="w-full rounded-md bg-primary px-4 py-3 text-center text-base font-semibold text-primary-foreground transition-all hover:bg-primary/90"
            @click="isMenuOpen = false"
          >
            Go to Dashboard
          </Link>
          <template v-else>
            <Link
              :href="login()"
              class="w-full rounded-md border border-muted px-4 py-3 text-center text-base font-medium transition-colors hover:bg-muted"
              @click="isMenuOpen = false"
            >
              Log in
            </Link>
            <Link
              v-if="canRegister"
              :href="register()"
              class="w-full rounded-md bg-primary px-4 py-3 text-center text-base font-semibold text-primary-foreground shadow-sm transition-all hover:bg-primary/90"
              @click="isMenuOpen = false"
            >
              Get Started
            </Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-16 lg:pt-32 lg:pb-40">
      <!-- Background Gradients -->
      <div class="absolute top-0 -z-10 h-full w-full">
        <div
          class="absolute top-[-10%] left-[-10%] h-[300px] w-[300px] rounded-full bg-primary/10 blur-[80px] sm:h-[500px] sm:w-[500px] sm:blur-[120px]"
        ></div>
        <div
          class="absolute right-[-10%] bottom-[-10%] h-[300px] w-[300px] rounded-full bg-success/10 blur-[80px] sm:h-[500px] sm:w-[500px] sm:blur-[120px]"
        ></div>
      </div>

      <div class="mx-auto max-w-7xl px-6 text-center">
        <div
          class="mb-8 inline-flex items-center gap-2 rounded-full border bg-muted/50 px-3 py-1 text-xs font-medium sm:text-sm"
        >
          <span
            class="flex h-2 w-2 animate-pulse rounded-full bg-primary"
          ></span>
          Record your Income and Expense with InexTracker
        </div>

        <h1
          class="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl"
        >
          Take Control of Your
          <span class="text-primary">Financial Future</span>
        </h1>

        <p
          class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-muted-foreground sm:mt-8 sm:text-lg lg:text-xl"
        >
          The simple, intuitive way to track your income, manage expenses, and
          visualize your financial growth. InexTracker helps you master your
          money.
        </p>

        <div
          class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
        >
          <Link
            v-if="!$page.props.auth.user"
            :href="register()"
            class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-4 text-base font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:bg-primary/90 sm:px-8 sm:text-lg lg:w-auto"
          >
            Start tracking for free
            <ArrowRight
              class="size-5 transition-transform group-hover:translate-x-1"
            />
          </Link>
          <Link
            v-else
            :href="dashboard()"
            class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-4 text-base font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:bg-primary/90 sm:px-8 sm:text-lg lg:w-auto"
          >
            Go to Dashboard
            <ArrowRight
              class="size-5 transition-transform group-hover:translate-x-1"
            />
          </Link>
          <a
            href="#features"
            class="w-full rounded-lg border bg-background px-6 py-4 text-base font-bold transition-all hover:bg-muted sm:px-8 sm:text-lg lg:w-auto"
          >
            Learn more
          </a>
        </div>

        <!-- Product Preview Slideshow -->
        <div
          class="relative mx-auto mt-16 max-w-5xl sm:mt-20"
          @mouseenter="stopAutoSlide"
          @mouseleave="startAutoSlide"
        >
          <div
            class="group relative overflow-hidden rounded-xl border bg-card p-1 shadow-2xl sm:p-2"
          >
            <!-- Slides Container -->
            <div class="relative aspect-video w-full overflow-hidden rounded-lg border">
              <div
                class="flex h-full w-full transition-transform duration-700 ease-in-out"
                :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
              >
                <div
                  v-for="(image, index) in previewImages"
                  :key="index"
                  class="h-full w-full flex-shrink-0"
                >
                  <img
                    :src="image.src"
                    :alt="image.alt"
                    class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.01]"
                  />
                  <!-- Optional: Slide Label -->
                  <div
                    class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/60 px-4 py-1.5 text-xs font-semibold text-white backdrop-blur-md transition-opacity duration-300 sm:text-sm"
                    :class="currentSlide === index ? 'opacity-100' : 'opacity-0'"
                  >
                    {{ image.title }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation Arrows -->
            <button
              class="absolute top-1/2 left-4 z-10 -translate-y-1/2 rounded-full border bg-background/80 p-2 text-foreground shadow-lg backdrop-blur-md transition-all hover:bg-background sm:left-6 sm:p-3"
              @click="prevSlide"
              aria-label="Previous slide"
            >
              <ChevronLeft class="size-5 sm:size-6" />
            </button>
            <button
              class="absolute top-1/2 right-4 z-10 -translate-y-1/2 rounded-full border bg-background/80 p-2 text-foreground shadow-lg backdrop-blur-md transition-all hover:bg-background sm:right-6 sm:p-3"
              @click="nextSlide"
              aria-label="Next slide"
            >
              <ChevronRight class="size-5 sm:size-6" />
            </button>
          </div>

          <!-- Slide Indicators (Dots) -->
          <div class="mt-6 flex justify-center gap-2">
            <button
              v-for="(_, index) in previewImages"
              :key="index"
              class="h-2 rounded-full transition-all duration-300"
              :class="
                currentSlide === index
                  ? 'w-8 bg-primary shadow-[0_0_10px_rgba(var(--primary),0.5)]'
                  : 'w-2 bg-muted-foreground/30 hover:bg-muted-foreground/50'
              "
              @click="currentSlide = index"
              :aria-label="`Go to slide ${index + 1}`"
            ></button>
          </div>
        </div>
      </div>
    </section>

    <!-- Feature Section -->
    <section id="features" class="bg-muted/30 py-16 sm:py-24">
      <div class="mx-auto max-w-7xl px-6">
        <div class="mb-12 text-center sm:mb-16">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
            Everything you need to succeed
          </h2>
          <p class="mt-4 text-muted-foreground">
            Powerful features to give you total clarity on your spending habits.
          </p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="flex flex-col rounded-xl border bg-card p-6 transition-all hover:-translate-y-1 hover:shadow-lg sm:p-8"
          >
            <div
              :class="[
                'mb-4 inline-flex size-12 items-center justify-center rounded-lg bg-muted p-3',
                feature.color,
              ]"
            >
              <component :is="feature.icon" class="size-6" />
            </div>
            <h3 class="mb-2 text-lg font-bold sm:text-xl">
              {{ feature.title }}
            </h3>
            <p class="text-sm leading-relaxed text-muted-foreground">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section (About) -->
    <section id="about" class="border-t py-16 sm:py-24">
      <div class="mx-auto max-w-7xl px-6">
        <div
          class="flex flex-col items-center justify-between gap-12 lg:flex-row"
        >
          <div class="max-w-xl text-center lg:text-left">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
              Stop wondering where your money went.
            </h2>
            <p class="mt-6 text-base text-muted-foreground sm:text-lg">
              We built InexTracker because we were tired of complex spreadsheets
              and confusing banking apps. Our mission is to provide a clean,
              visual platform that empowers individuals to achieve their
              financial goals.
            </p>
            <div class="mt-8 space-y-4">
              <div class="flex items-center gap-3 text-left">
                <div class="shrink-0 rounded-full bg-primary/10 p-1 text-primary">
                  <ShieldCheck class="size-5" />
                </div>
                <span class="text-sm font-medium sm:text-base"
                  >Bank-grade encryption for your peace of mind</span
                >
              </div>
              <div class="flex items-center gap-3 text-left">
                <div class="shrink-0 rounded-full bg-primary/10 p-1 text-primary">
                  <ShieldCheck class="size-5" />
                </div>
                <span class="text-sm font-medium sm:text-base"
                  >100% free for individual personal use</span
                >
              </div>
            </div>
          </div>

          <div class="grid w-full grid-cols-2 gap-3 sm:gap-4 lg:w-auto">
            <div class="rounded-2xl border bg-card p-4 text-center sm:p-8">
              <div class="text-3xl font-bold text-primary sm:text-4xl">99%</div>
              <p class="mt-1 text-xs font-medium text-muted-foreground sm:mt-2 sm:text-sm">
                User Satisfaction
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-4 text-center sm:p-8">
              <div class="text-3xl font-bold text-primary sm:text-4xl">24/7</div>
              <p class="mt-1 text-xs font-medium text-muted-foreground sm:mt-2 sm:text-sm">
                Data Access
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-4 text-center sm:p-8">
              <div class="text-3xl font-bold text-primary sm:text-4xl">10k+</div>
              <p class="mt-1 text-xs font-medium text-muted-foreground sm:mt-2 sm:text-sm">
                Active Users
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-4 text-center sm:p-8">
              <div class="text-3xl font-bold text-primary sm:text-4xl">Free</div>
              <p class="mt-1 text-xs font-medium text-muted-foreground sm:mt-2 sm:text-sm">
                No hidden costs
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-20">
      <div class="mx-auto max-w-5xl px-6">
        <div
          class="rounded-3xl bg-primary px-6 py-12 text-center text-primary-foreground shadow-2xl md:px-16 md:py-20"
        >
          <h2 class="text-3xl font-extrabold sm:text-5xl">
            Ready to take the first step?
          </h2>
          <p
            class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-primary-foreground/80 sm:text-lg"
          >
            Join thousands of people who have already improved their financial
            habits with InexTracker.
          </p>
          <div
            class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
          >
            <Link
              v-if="canRegister && !$page.props.auth.user"
              :href="register()"
              class="w-full rounded-xl bg-white px-8 py-4 text-lg font-bold text-primary shadow-lg shadow-black/10 transition-all hover:bg-muted sm:w-auto"
            >
              Create free account
            </Link>
            <Link
              v-if="!$page.props.auth.user"
              :href="login()"
              class="w-full rounded-xl border border-primary-foreground/30 px-8 py-4 text-lg font-bold text-primary-foreground transition-all hover:bg-white/10 sm:w-auto"
            >
              Log into account
            </Link>
            <Link
              v-else
              :href="dashboard()"
              class="w-full rounded-xl bg-white px-8 py-4 text-lg font-bold text-primary shadow-lg shadow-black/10 transition-all hover:bg-muted sm:w-auto"
            >
              Go to Dashboard
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t py-12 lg:py-20">
      <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-10 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5">
          <div class="sm:col-span-2 lg:col-span-2">
            <div class="flex items-center gap-2">
              <AppLogoIcon class="size-6 fill-current text-primary" />
              <span class="text-lg font-bold tracking-tight">InexTracker</span>
            </div>
            <p
              class="mt-4 max-w-xs text-sm leading-relaxed text-muted-foreground"
            >
              Empowering you to manage your finances with ease, precision, and
              visual clarity.
            </p>
          </div>

          <div class="grid grid-cols-2 gap-8 sm:col-span-2 md:grid-cols-3 lg:col-span-3">
            <div>
              <h4 class="mb-4 text-xs font-bold tracking-wider text-foreground uppercase sm:text-sm">
                Product
              </h4>
              <ul class="space-y-3 text-sm font-medium text-muted-foreground">
                <li>
                  <a href="#features" class="transition-colors hover:text-primary"
                    >Features</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Pricing</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Documentation</a
                  >
                </li>
              </ul>
            </div>

            <div>
              <h4 class="mb-4 text-xs font-bold tracking-wider text-foreground uppercase sm:text-sm">
                Company
              </h4>
              <ul class="space-y-3 text-sm font-medium text-muted-foreground">
                <li>
                  <a href="#about" class="transition-colors hover:text-primary"
                    >About</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Privacy Policy</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Terms of Service</a
                  >
                </li>
              </ul>
            </div>

            <div class="col-span-2 sm:col-span-1">
              <h4 class="mb-4 text-xs font-bold tracking-wider text-foreground uppercase sm:text-sm">
                Support
              </h4>
              <ul class="space-y-3 text-sm font-medium text-muted-foreground">
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Help Center</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Contact</a
                  >
                </li>
                <li>
                  <a href="#" class="transition-colors hover:text-primary"
                    >Feedback</a
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div
          class="mt-12 border-t pt-8 text-center text-sm text-muted-foreground"
        >
          <p>
            © {{ new Date().getFullYear() }} InexTracker. Built with passion for
            better financial health.
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Smooth scrolling for anchor links */
html {
  scroll-behavior: smooth;
}
</style>
