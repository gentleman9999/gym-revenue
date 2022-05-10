<template>
    <app-layout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <update-profile-information-form :user="$page.props.user" :addlData="addlData"/>

                    <jet-section-border />
                </div>


                <div v-if="$page.props.user.has_api_token">
                    <show-api-token-form class="mt-10 sm:mt-0" v-if="(!('is_being_impersonated' in $page.props.user))" :token="addlData['token']"/>
                    <jet-form-section v-else>
                        <template #title>
                            API Access
                        </template>
                        <template #description>
                            View your Access Token! It will bo in the Authorization header with a Bearer in front of it.
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-4">
                                <label>Viewing API tokens in impersonation mode is not authorized.</label>
                            </div>
                        </template>
                    </jet-form-section>

                    <jet-section-border />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword" class="w-1/2" style="margin:0 auto;">
                    <update-password-form class="mt-10 sm:mt-0" v-if="(!('is_being_impersonated' in $page.props.user))"/>
                    <jet-form-section v-else>
                        <template #title>
                            Update Password
                        </template>
                        <template #description>
                            Ensure your account is using a long, random password to stay secure.
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-4">
                                <label>Password Functions Not available in impersonation mode.</label>
                            </div>
                        </template>
                    </jet-form-section>

                    <jet-section-border />
                </div>

                <div class="w-full grid grid-cols-6 gap-6">
                    <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="col-span-2 sm:col-span-2">
                    <two-factor-authentication-form class="mt-10 sm:mt-0" v-if="(!('is_being_impersonated' in $page.props.user))"/>
                    <jet-form-section v-else>
                        <template #title>
                            Two Factor Authentication
                        </template>
                        <template #description>
                            Add additional security to your account using two factor authentication.
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-4">
                                <label>Two Factor Authentication Not available in impersonation mode.</label>
                            </div>
                        </template>
                    </jet-form-section>
                    <jet-section-border />
                </div>

                    <logout-other-browser-sessions-form :sessions="sessions" class="mt-10 sm:mt-0" v-if="(!('is_being_impersonated' in $page.props.user))"/>

                    <template v-if="$page.props.jetstream.hasAccountDeletionFeatures && (!('is_being_impersonated' in $page.props.user)) ">


                        <delete-user-form class="mt-10 sm:mt-0" />
                    </template>
                </div>


            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout'
    import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm'
    import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm'
    import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm'
    import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm'
    import JetFormSection from '@/Jetstream/FormSection'
    import ShowApiTokenForm from "@/Pages/Profile/Partials/ShowAPITokenForm";

    export default defineComponent({
        props: ['sessions', 'addlData'],

        components: {
            AppLayout,
            DeleteUserForm,
            JetSectionBorder,
            LogoutOtherBrowserSessionsForm,
            TwoFactorAuthenticationForm,
            UpdatePasswordForm,
            JetFormSection,
            UpdateProfileInformationForm,
            ShowApiTokenForm
        },
    })
</script>
