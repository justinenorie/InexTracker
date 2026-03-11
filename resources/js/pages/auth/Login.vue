<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { Spinner } from '@/components/ui/spinner'
import { register } from '@/routes'
import { store } from '@/routes/login'
import { request } from '@/routes/password'

defineProps<{
  status?: string
  canResetPassword: boolean
  canRegister: boolean
}>()

const showPassword = ref(false)
</script>

<template>
  <div
    class="flex min-h-svh flex-col items-center justify-center bg-background p-6 md:p-10"
  >
    <Head title="Log in" />

    <div class="w-full max-w-sm md:max-w-3xl lg:max-w-4xl">
      <Card class="overflow-hidden p-0 shadow-xl sm:rounded-xl">
        <CardContent class="grid p-0 md:grid-cols-2">
          <Form
            v-bind="store()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="p-6 sm:p-10 md:p-12 lg:p-16"
          >
            <FieldGroup>
              <div class="flex flex-col items-center gap-2 text-center">
                <h1 class="text-3xl font-extrabold tracking-tight">
                  Welcome back
                </h1>
                <p
                  class="text-sm text-balance text-muted-foreground sm:text-base"
                >
                  Enter your credentials to access your dashboard
                </p>
              </div>

              <div
                v-if="status"
                class="rounded-lg bg-success/10 p-3 text-center text-sm font-medium text-success"
              >
                {{ status }}
              </div>

              <Field class="mt-4">
                <FieldLabel for="email" class="font-semibold"
                  >Email Address</FieldLabel
                >
                <Input
                  id="email"
                  type="email"
                  name="email"
                  placeholder="name@example.com"
                  required
                  autofocus
                  autocomplete="email"
                  class="h-11"
                />
                <InputError :message="errors.email" />
              </Field>

              <Field>
                <div class="flex items-center">
                  <FieldLabel for="password" class="font-semibold"
                    >Password</FieldLabel
                  >
                  <Link
                    v-if="canResetPassword"
                    :href="request()"
                    class="ml-auto text-sm font-medium text-primary underline-offset-4 hover:underline"
                  >
                    Forgot password?
                  </Link>
                </div>
                <div class="relative">
                  <Input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="h-11 pr-10"
                  />
                  <button
                    type="button"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
                    @click="showPassword = !showPassword"
                  >
                    <component
                      :is="showPassword ? EyeOff : Eye"
                      class="h-4 w-4"
                    />
                  </button>
                </div>
                <InputError :message="errors.password" />
              </Field>

              <Field class="pt-2">
                <Button
                  type="submit"
                  class="h-11 w-full text-base font-bold shadow-lg shadow-primary/20"
                  :disabled="processing"
                >
                  <Spinner v-if="processing" />
                  Sign In
                </Button>
              </Field>

              <div
                class="mt-4 text-center text-sm text-muted-foreground"
                v-if="canRegister"
              >
                New here?
                <Link
                  :href="register()"
                  class="font-semibold text-primary underline-offset-4 hover:underline"
                  >Create an account</Link
                >
              </div>
            </FieldGroup>
          </Form>
          <div class="relative hidden bg-muted md:block">
            <div
              class="absolute inset-0 z-10 bg-primary/20 backdrop-blur-[2px]"
            ></div>
            <img
              src="/inextracker_bg.png"
              alt="Background"
              class="absolute inset-0 h-full w-full object-cover grayscale-[0.2]"
            />
            <div
              class="relative z-20 flex h-full flex-col justify-between p-12 text-white"
            >
              <div class="flex items-center gap-2">
                <AppLogoIcon class="size-8 fill-current" />
                <span class="text-xl font-bold tracking-tight"
                  >InexTracker</span
                >
              </div>
              <div class="space-y-4">
                <p class="text-2xl leading-tight font-medium italic">
                  "Control your money, control your life. Tracking every penny
                  is the first step to financial freedom."
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
      <p class="mt-4 px-6 text-center text-xs text-muted-foreground">
        By clicking continue, you agree to our
        <a href="#" class="underline underline-offset-4 hover:text-primary"
          >Terms of Service</a
        >
        and
        <a href="#" class="underline underline-offset-4 hover:text-primary"
          >Privacy Policy</a
        >.
      </p>
    </div>
  </div>
</template>
