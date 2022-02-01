<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title>Trial Memberships</template>

        <template #description> Configure Trial Memberships</template>

        <template #form>
            <div class="col-span-6 sm:col-span-4 flex flex-col gap-16">
                <div
                    v-for="(trialMembershipType, index) in trialMembershipTypes" class="flex flex-col gap-4"
                >
                    <div class="form-control">
                        <jet-label :for="`type_name${index}`" value="Name"/>
                        <input
                            :id="`type_name${index}`"
                            type="text"
                            v-model="form.trialMembershipTypes[index].type_name"
                        />
                    </div>
                    <div class="form-control">
                        <jet-label :for="`type_name${index}`" value="Slug"/>
                        <input
                            :id="`slug${index}`"
                            type="text"
                            v-model="form.trialMembershipTypes[index].slug"
                        />
                    </div>
                    <div class="form-control">
                        <jet-label :for="`duration${index}`" value="Duration"/>
                        <input
                            :id="`duration${index}`"
                            type="number"
                            min="1"
                            v-model="
                                form.trialMembershipTypes[index].trial_length
                            "
                        />
                    </div>
                </div>
            </div>
            <jet-input-error :message="form.errors.services" class="mt-2"/>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {defineComponent} from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import {useForm} from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        trialMembershipTypes: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        const form = useForm({
            trialMembershipTypes: props.trialMembershipTypes.map(trialMembershipType => ({
                id: trialMembershipType.id,
                slug: trialMembershipType.slug,
                locations: trialMembershipType.locations,
                type_name: trialMembershipType.type_name,
                trial_length: trialMembershipType.trial_length
            })),
        });
        let handleSubmit = () => form.post(route("settings.trial-membership-types.update"));

        return {form, handleSubmit, maxTrialMembershipTypes: 5};
    },
});
</script>
