<template>
    <div class="login-container">
        <div class="login-top-bg">
            <div class="login-bg-effect top-8"></div>
            <div class="login-bg-effect top-24"></div>
            <div class="login-bg-effect top-40"></div>
        </div>
        <Button secondary class="login-btn" @click="$emit('toggle-mode')"
            >Sign In</Button
        >
        <application-logo class="lock-logo" />
        <div class="create-account">Create Account</div>
        <div>
            <div class="login-bg-effect bottom-8"></div>
            <div class="login-bg-effect bottom-24"></div>
            <div class="login-bg-effect bottom-40"></div>
        </div>
    </div>
</template>
<style scoped>
.login-container {
    @apply flex flex-col relative left-0 items-center bg-neutral overflow-clip md:!mx-0;
    margin-left: -1rem;
    margin-right: -1rem;
    margin-top: -2.5rem;
    height: calc(100vh - 128px);
}
.login-bg-effect {
    @apply absolute w-48 h-10 rounded rounded-full bg-base-content/5;
    right: -4rem;
}
.login-top-bg {
    height: 40vh;
    @apply border-b-2 border-secondary w-full bg-base-100;
    .login-bg-effect {
        left: -4rem;
    }
}
.lock-logo {
    @apply relative h-10 w-auto text-base-content;
    bottom: 10rem;
}
.create-account {
    @apply relative text-lg;
    bottom: 2rem;
}
.login-btn {
    @apply relative text-xl font-bold rounded rounded-full bottom-6 px-16;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
</script>
