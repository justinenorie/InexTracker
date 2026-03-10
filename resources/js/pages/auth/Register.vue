<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
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

    <div class="w-full max-w-sm md:max-w-4xl">
      <Card class="overflow-hidden p-0">
        <CardContent class="grid p-0 md:grid-cols-2">
          <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="p-6 md:p-8"
          >
            <FieldGroup>
              <div class="flex flex-col items-center gap-2 text-center">
                <h1 class="text-2xl font-bold">Create your account</h1>
                <p class="text-sm text-balance text-muted-foreground">
                  Enter your details below to create your account
                </p>
              </div>

              <Field>
                <FieldLabel for="name">Name</FieldLabel>
                <Input
                  id="name"
                  type="text"
                  name="name"
                  required
                  autofocus
                  autocomplete="name"
                  placeholder="Full name"
                />
                <InputError :message="errors.name" />
              </Field>

              <Field>
                <FieldLabel for="email">Email address</FieldLabel>
                <Input
                  id="email"
                  type="email"
                  name="email"
                  required
                  autocomplete="email"
                  placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
              </Field>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Field>
                  <FieldLabel for="password">Password</FieldLabel>
                  <div class="relative">
                    <Input
                      id="password"
                      :type="showPassword ? 'text' : 'password'"
                      name="password"
                      required
                      autocomplete="new-password"
                      placeholder="Password"
                      class="pr-10"
                    />
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      @click="showPassword = !showPassword"
                    >
                      <component :is="showPassword ? EyeOff : Eye" class="h-4 w-4" />
                    </button>
                  </div>
                  <InputError :message="errors.password" />
                </Field>

                <Field>
                  <FieldLabel for="password_confirmation">Confirm password</FieldLabel>
                  <div class="relative">
                    <Input
                      id="password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      name="password_confirmation"
                      required
                      autocomplete="new-password"
                      placeholder="Confirm password"
                      class="pr-10"
                    />
                    <button
                      type="button"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <component :is="showConfirmPassword ? EyeOff : Eye" class="h-4 w-4" />
                    </button>
                  </div>
                  <InputError :message="errors.password_confirmation" />
                </Field>
              </div>

              <Field>
                <FieldLabel for="initial_balance"
                  >Initial Balance (Optional)</FieldLabel
                >
                <Input
                  id="initial_balance"
                  type="number"
                  name="initial_balance"
                  placeholder="0.00"
                  step="0.01"
                />
                <FieldDescription
                  >Set your starting wallet balance.</FieldDescription
                >
                <InputError :message="errors.initial_balance" />
              </Field>

              <Field>
                <Button type="submit" class="w-full" :disabled="processing">
                  <Spinner v-if="processing" />
                  Create account
                </Button>
              </Field>

              <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <Link :href="login()" class="underline underline-offset-4"
                  >Log in</Link
                >
              </div>
            </FieldGroup>
          </Form>
          <div class="relative hidden bg-muted md:block">
            <img
              src="/inextracker_bg.png"
              alt="Background"
              class="absolute inset-0 h-full w-full object-cover dark:brightness-[0.4]"
            />
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
