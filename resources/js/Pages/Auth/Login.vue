<template>
    <Head title="Log in" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <jet-validation-errors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" value="Email Address or Username" />
                <input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password*" />
                <input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input type="checkbox" class="checkbox" name="remember" v-model="form.remember" />
                    <span class="ml-2 text-sm ">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4">
                <!--<inertia-link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm  hover:">
                    Forgot your password?
                </inertia-link>-->

                <Button class="pl-8 pr-8 ml-4 bg-secondary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Login
                </Button>
            </div>
        </form>
    </jet-authentication-card>
    
    <div class="justify-center flex items-center sm:max-w-md pb-6 border-b border-secondary border-1" style="margin:0 auto;"><inertia-link v-on:click="comingSoon()" v-if="canResetPassword" class=" text-sm  hover:">
        Email me a login link
    </inertia-link></div>
    <div class="justify-center flex items-center sm:max-w-md pb-4 border-b border-secondary border-1" style="margin:0 auto;"><inertia-link v-if="canResetPassword" :href="route('password.request')" class="mt-4 text-sm  hover:">
        Lost your password?
    </inertia-link></div>


</template>

<script>
    import { defineComponent } from 'vue'
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo'
    import Button from '@/Components/Button'


    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'
    import { Head } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Head,
            JetAuthenticationCard,
            JetAuthenticationCardLogo,
            Button,
            JetLabel,
            JetValidationErrors,
        },

        props: {
            canResetPassword: Boolean,
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false
                })
            }
        },

        methods: {
            submit() {
                this.form
                    .transform(data => ({
                        ... data,
                        remember: this.form.remember ? 'on' : ''
                    }))
                    .post(this.route('login'), {
                        onFinish: () => this.form.reset('password'),
                    })
            },
            comingSoon() {
                new Noty({
                    type: 'warning',
                    theme: 'sunset',
                    text: 'Feature Coming Soon!',
                    timeout: 7500
                }).show();
            }
        }
    })
</script>
