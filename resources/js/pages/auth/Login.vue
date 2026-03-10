<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
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

    <div class="w-full max-w-sm md:max-w-3xl">
      <Card class="overflow-hidden p-0">
        <CardContent class="grid p-0 md:grid-cols-2">
          <Form
            v-bind="store()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="p-6 md:p-8"
          >
            <FieldGroup>
              <div class="flex flex-col items-center gap-2 text-center">
                <h1 class="text-2xl font-bold">Welcome back</h1>
                <p class="text-sm text-balance text-muted-foreground">
                  Login to your Expense Tracker account
                </p>
              </div>

              <div
                v-if="status"
                class="mb-4 text-center text-sm font-medium text-green-600"
              >
                {{ status }}
              </div>

              <Field>
                <FieldLabel for="email"> Email </FieldLabel>
                <Input
                  id="email"
                  type="email"
                  name="email"
                  placeholder="m@example.com"
                  required
                  autofocus
                  autocomplete="email"
                />
                <InputError :message="errors.email" />
              </Field>

              <Field>
                <div class="flex items-center">
                  <FieldLabel for="password"> Password </FieldLabel>
                  <Link
                    v-if="canResetPassword"
                    :href="request()"
                    class="ml-auto text-sm underline-offset-2 hover:underline"
                  >
                    Forgot your password?
                  </Link>
                </div>
                <div class="relative">
                  <Input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your Password"
                    class="pr-10"
                  />
                  <button
                    type="button"
                    class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
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
                <Button type="submit" class="w-full" :disabled="processing">
                  <Spinner v-if="processing" />
                  Login
                </Button>
              </Field>

              <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
              >
                Don't have an account?
                <Link :href="register()" class="underline underline-offset-4"
                  >Sign up</Link
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
