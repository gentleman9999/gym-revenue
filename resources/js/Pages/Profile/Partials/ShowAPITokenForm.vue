<template>
    <jet-form-section>
        <template #title> API Access </template>

        <template #description>
            View your Access Token! It will bo in the Authorization header with
            a Bearer in front of it.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="mockup-code" value="In the Request Header" />
                <div class="mockup-code border border-secondary mt-4">
                    <pre
                        data-prefix=""
                    ><code>Authorization: Bearer {{ token }}</code></pre>
                </div>
            </div>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default {
    name: "ShowAPITokenForm",
    components: {
        JetActionMessage,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: ["token"],
};
</script>

<style scoped></style>
