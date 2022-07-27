<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Social Media</template>

        <template #description>
            Add the URL's to the related social media sites below:
        </template>

        <template #form>
            <div
                v-for="{ name, value } in availableSocialMedias"
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="facebook" :value="value" />
                <input :id="name" type="text" v-model="form[name]" />
                <jet-input-error :message="form.errors[name]" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </Button>
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
import { useGymRevForm } from "@/utils";

export default defineComponent({
    components: {
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        socialMedias: {
            type: Array,
            default: [],
        },
        availableSocialMedias: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        let socialMedias = Object.fromEntries(
            props.availableSocialMedias.map(({ name }) => [name, ""])
        );

        if (props.socialMedias && Object.entries(props.socialMedias)?.length) {
            socialMedias = { ...socialMedias, ...props.socialMedias };
        }
        console.log("socialMedias", { socialMedias: socialMedias });
        const form = useGymRevForm(socialMedias);
        console.log("socialform", { socialForm: form });
        let handleSubmit = () =>
            form
                .dirty()
                .transform((data) => ({ socialMedias: data }))
                .put(route("settings.social-media.update"));

        return { form, handleSubmit };
    },
});
</script>
