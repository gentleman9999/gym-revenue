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
                            v-model="
                                form.trialMembershipTypes[index].locations
                            "
                            :id="`locations${index}`"
                            mode="tags"
                            :close-on-select="false"
                            :create-option="true"
                            :options="locations.map(location=>({label: location.name, value: location.id}))"
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
            default: []
        }
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

        const multiselectClasses= {
            container: 'relative mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-2 border-base-content border-opacity-20 rounded-lg bg-base-100 text-base leading-snug outline-none min-h-12',
            containerDisabled: 'cursor-default bg-base-200',
            containerOpen: 'rounded-b-none',
            containerOpenTop: 'rounded-t-none',
            containerActive: 'ring ring-primary',
            singleLabel: 'flex items-center h-full max-w-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 pr-16 box-border',
            singleLabelText: 'overflow-ellipsis overflow-hidden block whitespace-nowrap max-w-full',
            multipleLabel: 'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5',
            search: 'w-full absolute inset-0 outline-none focus:ring-0 appearance-none box-border border-0 text-base font-sans bg-base-100 rounded pl-3.5',
            tags: 'flex-grow flex-shrink flex flex-wrap items-center mt-1 pl-2',
            tag: 'bg-primary text-base-content text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap',
            tagDisabled: 'pr-2 opacity-50',
            tagRemove: 'flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group',
            tagRemoveIcon: 'bg-multiselect-remove text-base-con bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60',
            tagsSearchWrapper: 'inline-block relative mx-1 mb-1 flex-grow flex-shrink h-full',
            tagsSearch: 'absolute inset-0 border-0 outline-none focus:ring-0 appearance-none p-0 text-base font-sans box-border w-full',
            tagsSearchCopy: 'invisible whitespace-pre-wrap inline-block h-px',
            placeholder: 'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-base-content text-opacity-50',
            caret: 'bg-multiselect-caret bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-none',
            caretOpen: 'rotate-180 pointer-events-auto',
            clear: 'pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80',
            clearIcon: 'bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block',
            spinner: 'bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0',
            dropdown: 'max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-100 flex flex-col rounded-b',
            dropdownTop: '-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t',
            dropdownHidden: 'hidden',
            options: 'flex flex-col p-0 m-0 list-none',
            optionsTop: 'flex-col-reverse',
            group: 'p-0 m-0',
            groupLabel: 'flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-base-300 cursor-default leading-normal',
            groupLabelPointable: 'cursor-pointer',
            groupLabelPointed: 'bg-base-300 text-base-content text-opacity-70',
            groupLabelSelected: 'bg-green-600 text-base-content',
            groupLabelDisabled: 'bg-base-200 text-base-content text-opacity-50 cursor-not-allowed',
            groupLabelSelectedPointed: 'bg-green-600 text-base-content opacity-90',
            groupLabelSelectedDisabled: 'text-green-100 bg-green-600 bg-opacity-50 cursor-not-allowed',
            groupOptions: 'p-0 m-0',
            option: 'flex items-center justify-start box-border text-left cursor-pointer text-base leading-snug py-2 px-3',
            optionPointed: 'bg-primary',
            optionSelected: 'text-base-content bg-green-500',
            optionDisabled: 'text-base-content text-opacity-50 cursor-not-allowed',
            optionSelectedPointed: 'text-base-content bg-green-500 opacity-90',
            optionSelectedDisabled: 'text-green-100 bg-green-500 bg-opacity-50 cursor-not-allowed',
            noOptions: 'py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left',
            noResults: 'py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left',
            fakeInput: 'bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent',
            spacer: 'h-9 py-px box-content',
        };

        return {
            form,
            handleSubmit,
            handleClickPlusIcon,
            maxTrialMembershipTypes: 5,
            setSlugInputRef,
            setTypeNameInputRef,
            setTrialLengthInputRef,
            multiselectClasses
        };
    },
});
</script>
