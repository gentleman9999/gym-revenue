<template>
  <div class="flex items-center">
    <div class="flex w-full bg-base-300 shadow rounded">
      <dropdown align="left" width="60">
        <template #trigger>
          <span class="inline-flex rounded-md h-full">
            <button
              type="button"
              class="
                inline-flex
                items-center
                px-3
                py-2
                border border-transparent
                text-sm
                leading-4
                font-medium
                rounded-md

                bg-base-300
                hover:bg-base-100 hover:
                focus:outline-none focus:bg-base-100
                active:bg-base-100
                transition
              "
            >
              Filter

              <svg
                class="ml-2 -mr-0.5 h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </span>
        </template>

        <template #content>
          <div class="w-60">
            <div class="block px-4 py-2 text-xs ">Filters</div>

            <div
              slot="dropdown"
              class="px-4 py-6 w-screen shadow-xl bg-base-300 rounded"
              :style="{ maxWidth: `100%` }"
            >
              <slot />
            </div>
          </div>
        </template>
      </dropdown>
      <input
        class="relative w-full px-6 py-3 rounded-r focus:ring"
        autocomplete="off"
        type="text"
        name="search"
        placeholder="Search…"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
    <button
      class="
        ml-3
        text-sm
      "
      type="button"
      @click="$emit('reset')"
    >
      Reset
    </button>
  </div>
</template>

<script>
import Dropdown from "@/Jetstream/Dropdown";
import { defineComponent } from "vue";

export default defineComponent({
  components: {
    Dropdown,
  },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },
});
</script>
