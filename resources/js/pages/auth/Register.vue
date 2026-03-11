<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import {
  Field,
  FieldDescription,
  FieldGroup,
  FieldLabel,
} from '@/components/ui/field'
import { Input } from '@/components/ui/input'
import { Spinner } from '@/components/ui/spinner'
import { login } from '@/routes'
import { store } from '@/routes/register'

const showPassword = ref(false)
const showConfirmPassword = ref(false)
</script>

<template>
  <div
    class="flex min-h-svh flex-col items-center justify-center bg-background p-6 md:p-10"
  >
    <Head title="Register" />

    <div class="w-full max-w-sm md:max-w-4xl lg:max-w-5xl">
      <Card class="overflow-hidden p-0 shadow-xl sm:rounded-xl">
        <CardContent class="grid p-0 md:grid-cols-2">
          <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="p-6 sm:p-10 md:p-12 lg:p-16"
          >
            <FieldGroup>
              <div class="flex flex-col items-center gap-2 text-center">
                <h1 class="text-3xl font-extrabold tracking-tight">
                  Create your account
                </h1>
                <p
                  class="text-sm text-balance text-muted-foreground sm:text-base"
                >
                  Join InexTracker and start your financial journey today
                </p>
              </div>

              <Field class="mt-4">
                <FieldLabel for="name" class="font-semibold"
                  >Full Name</FieldLabel
                >
                <Input
                  id="name"
                  type="text"
                  name="name"
                  required
                  autofocus
                  autocomplete="name"
                  placeholder="John Doe"
                  class="h-11"
                />
                <InputError :message="errors.name" />
              </Field>

              <Field>
                <FieldLabel for="email" class="font-semibold"
                  >Email Address</FieldLabel
                >
                <Input
                  id="email"
                  type="email"
                  name="email"
                  required
                  autocomplete="email"
                  placeholder="name@example.com"
                  class="h-11"
                />
                <InputError :message="errors.email" />
              </Field>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Field>
                  <FieldLabel for="password" class="font-semibold"
                    >Password</FieldLabel
                  >
                  <div class="relative">
                    <Input
                      id="password"
                      :type="showPassword ? 'text' : 'password'"
                      name="password"
                      required
                      autocomplete="new-password"
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

                <Field>
                  <FieldLabel for="password_confirmation" class="font-semibold"
                    >Confirm Password</FieldLabel
                  >
                  <div class="relative">
                    <Input
                      id="password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      name="password_confirmation"
                      required
                      autocomplete="new-password"
                      placeholder="••••••••"
                      class="h-11 pr-10"
                    />
                    <button
                      type="button"
                      class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <component
                        :is="showConfirmPassword ? EyeOff : Eye"
                        class="h-4 w-4"
                      />
                    </button>
                  </div>
                  <InputError :message="errors.password_confirmation" />
                </Field>
              </div>

              <Field>
                <FieldLabel for="initial_balance" class="font-semibold"
                  >Initial Balance (Optional)</FieldLabel
                >
                <Input
                  id="initial_balance"
                  type="number"
                  name="initial_balance"
                  placeholder="0.00"
                  step="0.01"
                  class="h-11"
                />
                <FieldDescription
                  >Set your starting wallet balance.</FieldDescription
                >
                <InputError :message="errors.initial_balance" />
              </Field>

              <Field class="pt-2">
                <Button
                  type="submit"
                  class="h-11 w-full text-base font-bold shadow-lg shadow-primary/20"
                  :disabled="processing"
                >
                  <Spinner v-if="processing" />
                  Create Account
                </Button>
              </Field>

              <div class="mt-4 text-center text-sm text-muted-foreground">
                Already have an account?
                <Link
                  :href="login()"
                  class="font-semibold text-primary underline-offset-4 hover:underline"
                  >Log in</Link
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
                  "The goal is not more money. The goal is living life on your
                  own terms. Start tracking and take charge."
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
