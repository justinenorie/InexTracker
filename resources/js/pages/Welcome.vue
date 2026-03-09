<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
  LayoutDashboard,
  PieChart as PieChartIcon,
  Tags,
  ShieldCheck,
  ArrowRight,
} from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { dashboard, login, register } from '@/routes'

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
      class="sticky top-0 z-50 w-full border-b bg-background/80 backdrop-blur-md"
    >
      <div class="mx-auto flex max-w-7xl items-center justify-between p-4 px-6">
        <div class="flex items-center gap-2">
          <AppLogoIcon class="size-8 fill-current text-primary" />
          <span class="text-xl font-bold tracking-tight">InexTracker</span>
        </div>

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
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-20 lg:pt-32 lg:pb-40">
      <!-- Background Gradients -->
      <div class="absolute top-0 -z-10 h-full w-full">
        <div
          class="absolute top-[-10%] left-[-10%] h-[500px] w-[500px] rounded-full bg-primary/10 blur-[120px]"
        ></div>
        <div
          class="absolute right-[-10%] bottom-[-10%] h-[500px] w-[500px] rounded-full bg-success/10 blur-[120px]"
        ></div>
      </div>

      <div class="mx-auto max-w-7xl px-6 text-center">
        <div
          class="mb-8 inline-flex items-center gap-2 rounded-full border bg-muted/50 px-3 py-1 text-sm font-medium"
        >
          <span
            class="flex h-2 w-2 animate-pulse rounded-full bg-primary"
          ></span>
          Record your Income and Expense with InexTracker
        </div>

        <h1
          class="mx-auto max-w-4xl text-5xl font-extrabold tracking-tight sm:text-7xl"
        >
          Take Control of Your
          <span class="text-primary">Financial Future</span>
        </h1>

        <p
          class="mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-muted-foreground"
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
            class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-8 py-4 text-lg font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:bg-primary/90 sm:w-auto"
          >
            Start tracking for free
            <ArrowRight
              class="size-5 transition-transform group-hover:translate-x-1"
            />
          </Link>
          <Link
            v-else
            :href="dashboard()"
            class="group flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-8 py-4 text-lg font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:bg-primary/90 sm:w-auto"
          >
            Go to Dashboard
            <ArrowRight
              class="size-5 transition-transform group-hover:translate-x-1"
            />
          </Link>
          <a
            href="#features"
            class="w-full rounded-lg border bg-background px-8 py-4 text-lg font-bold transition-all hover:bg-muted sm:w-auto"
          >
            Learn more
          </a>
        </div>

        <!-- Product Preview -->
        <div
          class="group relative mx-auto mt-20 max-w-5xl overflow-hidden rounded-xl border bg-card p-2 shadow-2xl"
        >
          <img
            src="/dashboard.png"
            alt="InexTracker Dashboard"
            class="aspect-video w-full rounded-lg border object-cover shadow-inner transition-transform duration-500 group-hover:scale-[1.01]"
          />
        </div>
      </div>
    </section>

    <!-- Feature Section -->
    <section id="features" class="bg-muted/30 py-24">
      <div class="mx-auto max-w-7xl px-6">
        <div class="mb-16 text-center">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
            Everything you need to succeed
          </h2>
          <p class="mt-4 text-muted-foreground">
            Powerful features to give you total clarity on your spending habits.
          </p>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="rounded-xl border bg-card p-8 transition-all hover:-translate-y-1 hover:shadow-lg"
          >
            <div
              :class="[
                'mb-4 inline-flex rounded-lg bg-muted p-3',
                feature.color,
              ]"
            >
              <component :is="feature.icon" class="size-6" />
            </div>
            <h3 class="mb-2 text-xl font-bold">
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
    <section id="about" class="border-t py-24">
      <div class="mx-auto max-w-7xl px-6">
        <div
          class="flex flex-col items-center justify-between gap-12 lg:flex-row"
        >
          <div class="max-w-xl">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
              Stop wondering where your money went.
            </h2>
            <p class="mt-6 text-lg text-muted-foreground">
              We built InexTracker because we were tired of complex spreadsheets
              and confusing banking apps. Our mission is to provide a clean,
              visual platform that empowers individuals to achieve their
              financial goals.
            </p>
            <div class="mt-8 space-y-4">
              <div class="flex items-center gap-3">
                <div class="rounded-full bg-primary/10 p-1 text-primary">
                  <ShieldCheck class="size-5" />
                </div>
                <span class="font-medium"
                  >Bank-grade encryption for your peace of mind</span
                >
              </div>
              <div class="flex items-center gap-3">
                <div class="rounded-full bg-primary/10 p-1 text-primary">
                  <ShieldCheck class="size-5" />
                </div>
                <span class="font-medium"
                  >100% free for individual personal use</span
                >
              </div>
            </div>
          </div>

          <div class="grid w-full grid-cols-2 gap-4 lg:w-auto">
            <div class="rounded-2xl border bg-card p-8 text-center">
              <div class="text-4xl font-bold text-primary">99%</div>
              <p class="mt-2 text-sm font-medium text-muted-foreground">
                User Satisfaction
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-8 text-center">
              <div class="text-4xl font-bold text-primary">24/7</div>
              <p class="mt-2 text-sm font-medium text-muted-foreground">
                Data Access
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-8 text-center">
              <div class="text-4xl font-bold text-primary">10k+</div>
              <p class="mt-2 text-sm font-medium text-muted-foreground">
                Active Users
              </p>
            </div>
            <div class="rounded-2xl border bg-card p-8 text-center">
              <div class="text-4xl font-bold text-primary">Free</div>
              <p class="mt-2 text-sm font-medium text-muted-foreground">
                No hidden costs
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
      <div class="mx-auto max-w-5xl px-6">
        <div
          class="rounded-3xl bg-primary px-8 py-12 text-center text-primary-foreground shadow-2xl md:px-16 md:py-20"
        >
          <h2 class="text-3xl font-extrabold sm:text-5xl">
            Ready to take the first step?
          </h2>
          <p
            class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-primary-foreground/80"
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
        <div class="grid gap-12 md:grid-cols-4 lg:grid-cols-5">
          <div class="md:col-span-2 lg:col-span-2">
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
            <div class="mt-6 flex gap-4"></div>
          </div>

          <div>
            <h4 class="mb-4 text-sm font-bold tracking-wider uppercase">
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
            <h4 class="mb-4 text-sm font-bold tracking-wider uppercase">
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

          <div>
            <h4 class="mb-4 text-sm font-bold tracking-wider uppercase">
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
