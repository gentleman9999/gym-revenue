<template>
    <member-contact v-if="mode === 'contact'" @toggle-mode="toggleMode" />
    <member-list v-if="mode === 'list'" @toggle-mode="toggleMode('contact')" />
    <member-note v-if="mode === 'note'" @toggle-mode="toggleMode('contact')" />
</template>
<style scoped></style>
<script setup>
import { ref } from "vue";
import MemberContact from "./member-contact.vue";
import MemberList from "./member-list.vue";
import MemberNote from "./member-note.vue";

const mode = ref("contact");

const toggleMode = (val) => {
    console.log("toggoleMode", val);
    mode.value = val;
};

defineExpose({ mode });
</script>
