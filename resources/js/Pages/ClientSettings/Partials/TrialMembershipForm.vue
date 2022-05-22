<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Trial Memberships</template>

        <template #description> Configure Trial Memberships</template>

        <template #form>
            <div class="col-span-6 sm:col-span-4 flex flex-col gap-16">
                <div
                    v-for="(
                        trialMembershipType, index
                    ) in form.trialMembershipTypes"
                    class="flex flex-col gap-4"
                >
                    <div class="form-control">
                        <jet-label :for="`type_name${index}`" value="Name" />
                        <input
                            :id="`type_name${index}`"
                            type="text"
                            v-model="form.trialMembershipTypes[index].type_name"
                            :ref="setTypeNameInputRef"
                        />
                    </div>
                    <div class="form-control">
                        <jet-label :for="`type_name${index}`" value="Slug" />
                        <input
                            :id="`slug${index}`"
                            type="text"
                            v-model="form.trialMembershipTypes[index].slug"
                            :ref="setSlugInputRef"
                        />
                    </div>
                    <div class="form-control">
                        <jet-label :for="`duration${index}`" value="Duration" />
                        <input
                            :id="`duration${index}`"
                            type="number"
                            min="1"
                            v-model="
                                form.trialMembershipTypes[index].trial_length
                            "
                            :ref="setTrialLengthInputRef"
                        />
                    </div>
                    <div class="form-control">
                        <jet-label
                            :for="`locations${index}`"
                            value="Locations"
                        />
                        <multiselect
                            v-model="form.trialMembershipTypes[index].locations"
                            :id="`locations${index}`"
                            mode="tags"
                            :close-on-select="false"
                            :create-option="true"
                            :options="
                                locations.map((location) => ({
                                    label: location.name,
                                    value: location.id,
                                }))
                            "
                            :classes="multiselectClasses"
                        />
                    </div>
                </div>
                <div class="flex flex-row justify-center py-2">
                    <button type="button" @click="handleClickPlusIcon">
                        <font-awesome-icon
                            icon="plus"
                            size="2x"
                            class="opacity-50 hover:opacity-100 transition-opacity"
                        />
                    </button>
                </div>
            </div>
            <jet-input-error :message="form.errors.services" class="mt-2" />
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
import { defineComponent, ref } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import Multiselect from "@vueform/multiselect";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import { useForm } from "@inertiajs/inertia-vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import "@vueform/multiselect/themes/default.css";
import { getDefaultMultiselectTWClasses } from "@/utils";

library.add(faPlus);

export default defineComponent({
    components: {
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        FontAwesomeIcon,
        Multiselect,
    },
    props: {
        trialMembershipTypes: {
            type: Array,
            default: [],
        },
        locations: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        const slugInputs = ref([]);
        const typeNameInputs = ref([]);
        const trialLengthInputs = ref([]);

        const setSlugInputRef = (el) => {
            if (el) {
                slugInputs.value.push(el);
            }
        };
        const setTypeNameInputRef = (el) => {
            if (el) {
                typeNameInputs.value.push(el);
            }
        };
        const setTrialLengthInputRef = (el) => {
            if (el) {
                trialLengthInputs.value.push(el);
            }
        };
        const form = useForm({
            trialMembershipTypes: props.trialMembershipTypes.map(
                (trialMembershipType) => ({
                    id: trialMembershipType.id,
                    slug: trialMembershipType.slug,
                    locations: trialMembershipType.locations,
                    type_name: trialMembershipType.type_name,
                    trial_length: trialMembershipType.trial_length,
                })
            ),
        });
        let handleSubmit = () =>
            form.post(route("settings.trial-membership-types.update"));

        const handleClickPlusIcon = () => {
            const lastTrialMembershipType =
                form.trialMembershipTypes[form.trialMembershipTypes.length - 1];
            if (
                !lastTrialMembershipType.type_name ||
                !lastTrialMembershipType.slug ||
                !lastTrialMembershipType.trial_length
            ) {
                focusLastInput();
                return;
            }
            form.trialMembershipTypes.push({
                id: null,
                slug: null,
                locations: null,
                type_name: null,
                trial_length: null,
            });
            setTimeout(focusLastInput, 100);
        };

        const focusLastInput = () => {
            const lastTrialMembershipType =
                form.trialMembershipTypes[form.trialMembershipTypes.length - 1];

            if (!lastTrialMembershipType.type_name) {
                typeNameInputs.value[typeNameInputs.value.length - 1].focus();
                return;
            }

            if (!lastTrialMembershipType.slug) {
                slugInputs.value[slugInputs.value.length - 1].focus();
                return;
            }

            if (!lastTrialMembershipType.trial_length) {
                trialLengthInputs.value[
                    trialLengthInputs.value.length - 1
                ].focus();
                return;
            }
        };

        return {
            form,
            handleSubmit,
            handleClickPlusIcon,
            maxTrialMembershipTypes: 5,
            setSlugInputRef,
            setTypeNameInputRef,
            setTrialLengthInputRef,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
});
</script>
