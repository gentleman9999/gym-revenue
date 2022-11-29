<template>
    <div class="login-scr-container">
        <application-logo class="login-logo" />
        <input class="login-input" type="email" />
        <input class="login-input" type="password" />
        <Button
            secondary
            class="login-btn"
            @click="$inertia.visit(route('checkin.index'))"
            >Sign In</Button
        >
        <div class="mt-8">Forgot Password?</div>
        <div class="login-bg-effect login-bg-effect-top top-8"></div>
        <div class="login-bg-effect login-bg-effect-top top-24"></div>
        <div class="login-bg-effect login-bg-effect-top top-40"></div>
        <div class="login-bg-effect login-bg-effect-bottom bottom-8"></div>
        <div class="login-bg-effect login-bg-effect-bottom bottom-24"></div>
        <div class="login-bg-effect login-bg-effect-bottom bottom-40"></div>
    </div>
</template>
<style scoped>
.login-scr-container {
    @apply flex flex-col relative left-0 items-center justify-center overflow-clip md:!mx-0;
    margin-left: -1rem;
    margin-right: -1rem;
    margin-top: -2.5rem;
    height: calc(100vh - 128px);
}
.login-bg-effect {
    @apply absolute w-48 h-10 rounded rounded-full bg-base-content/5;
}
.login-bg-effect-top {
    left: -4rem;
}
.login-bg-effect-bottom {
    right: -4rem;
}

.login-logo {
    @apply h-10 w-auto text-base-content mb-8;
}
.login-btn {
    @apply font-bold rounded mt-4 text-base;
}
.login-input {
    @apply bg-base-content/20 rounded h-8 mt-3 border-none outline-none text-center;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
</script>
