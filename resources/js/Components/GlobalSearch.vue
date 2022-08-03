<template>
    <form @submit.prevent="submit">
        <div class="relative form-control" @mouseleave="toggleInput(false)">
            <Combobox
                v-model="selected"
                nullable
                @update:modelValue="handleCombo"
            >
                <div class="input-group">
                    <ComboboxInput
                        type="text"
                        placeholder="Search…"
                        class="input input-bordered search-input"
                        :class="{
                            'invisible w-0': !showInput,
                            'w-56': showInput,
                        }"
                        :value="term"
                        :display-value="() => props.initialValue"
                        @change="term = $event.target.value"
                        @focus="showOptions = true"
                        @blur="showOptions = false"
                        id="global-search-input"
                        autocomplete="off"
                    />
                    <Button :ghost="!showInput" @mouseenter="toggleInput(true)">
                        <search-icon />
                    </Button>
                </div>
                <TransitionRoot
                    leave="transition ease-in duration-100"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                    :show="showOptions && term !== ''"
                >
                    <div
                        class="absolute mt-1 max-h-66 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                    >
                        <ComboboxOptions v-if="term !== ''">
                            <div
                                v-if="search_hints.total === 0 && term !== ''"
                                class="relative cursor-default select-none py-2 px-4 text-gray-700"
                            >
                                Nothing found.
                            </div>

                            <ComboboxOption
                                v-for="hint in search_hints?.data"
                                as="template"
                                :key="hint.id"
                                :value="hint"
                                v-slot="{ selected, active }"
                            >
                                <li
                                    class="relative cursor-default select-none py-2 pl-10 pr-4"
                                    :class="{
                                        'bg-teal-600 text-white': active,
                                        'text-gray-900': !active,
                                    }"
                                >
                                    <span
                                        class="block truncate"
                                        :class="{
                                            'font-medium': selected,
                                            'font-normal': !selected,
                                        }"
                                    >
                                        {{ hint.name }}
                                    </span>
                                    <span
                                        class="block truncate text-base opacity-50 !text-xs"
                                    >
                                        {{ hint.type }}
                                    </span>
                                    <span
                                        v-if="selected"
                                        class="absolute inset-y-0 left-0 flex items-center pl-3"
                                        :class="{
                                            'text-white': active,
                                            'text-teal-600': !active,
                                        }"
                                    >
                                        <font-awesome-icon
                                            :icon="['fas', 'check']"
                                            size="lg"
                                        />
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                        <button
                            v-if="search_hints.total > search_hints.per_page"
                            class="relative select-none py-4 px-4 opacity-50 text-base-300"
                        >
                            View all {{ search_hints.total }} results...
                        </button>
                    </div>
                </TransitionRoot>
            </Combobox>
        </div>
    </form>
</template>
<style scoped>
.search-input {
    transition: width 650ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
</style>
<script setup>
import { ref, computed, watch } from "vue";
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
} from "@headlessui/vue";
import Button from "@/Components/Button.vue";
import SearchIcon from "./Icons/Search.vue";
import { Inertia } from "@inertiajs/inertia";
import throttle from "lodash/throttle";

import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCheck } from "@fortawesome/pro-solid-svg-icons";
library.add(faCheck);

const props = defineProps({
    // term: {
    //     type: String,
    // },
    size: {
        type: String,
        default: "sm",
    },
    initialValue: {
        type: String,
        default: "",
    },
    animate: {
        type: Boolean,
        default: true,
    },
});

const submit = () => {
    Inertia.get(route("searches"), {
        term: term.value,
    });
};

const showOptions = ref(false);

const search_hints = ref([]);

let selected = ref({});

let term = ref(props.initialValue);
const searchHandler = throttle(function () {
    if (term.value) {
        axios
            .post("/searches/searchahead", { search: term.value })
            .then(function (response) {
                search_hints.value = response.data;
            });
    }
}, 150);

watch([term], searchHandler);

const showInput = ref(!props.animate);
const toggleInput = (flag) => {
    showInput.value = flag || !props.animate || selected.name || term.value;
};
const handleCombo = (val) => {
    if (val?.link.includes("edit")) {
        Inertia.visit(route(val.link, val.id));
    } else {
        Inertia.visit(route(val.link));
    }
    // selected.value=hint;
    document.activeElement.blur();
    term.value = "";
};
</script>
