<template>
  <jet-dropdown align="right" width="48">
    <template #trigger>
      <button
        v-if="$page.props.jetstream.managesProfilePhotos"
        class="
          flex
          text-sm
          border-2 border-transparent
          rounded-full
          focus:outline-none focus:border-gray-300
          transition
        "
      >
        <img
          class="h-8 w-8 rounded-full object-cover"
          :src="$page.props.user.profile_photo_url"
          :alt="$page.props.user.name"
        />
      </button>

      <span v-else class="inline-flex rounded-md">
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
            text-gray-500
            bg-base-300
            hover:text-gray-700
            focus:outline-none
            transition
          "
        >
          {{ $page.props.user.name }}

          <svg
            class="ml-2 -mr-0.5 h-4 w-4"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </span>
    </template>

    <template #content>
      <!-- Account Management -->
      <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>

      <jet-dropdown-link :href="route('profile.show')">
        Profile
      </jet-dropdown-link>
      <!-- @todo - make these dynamic, as some users wont have access -->
      <jet-dropdown-link :href="route('profile.show')">
        User Management
      </jet-dropdown-link>
      <jet-dropdown-link :href="route('profile.show')">
        Invoices
      </jet-dropdown-link>

      <jet-dropdown-link
        :href="route('api-tokens.index')"
        v-if="$page.props.jetstream.hasApiFeatures"
      >
        API Tokens
      </jet-dropdown-link>

      <div class="border-t border-gray-100"></div>
      <!-- Extra Features -->
      <!-- @todo - make these dynamic, as some users wont have access -->
      <div class="block px-4 py-2 text-xs text-gray-400">Extras</div>
      <jet-dropdown-link :href="route('workout-generator')">
        Workout Generator
      </jet-dropdown-link>
      <jet-dropdown-link :href="route('workout-generator')">
        Sales Slideshow
      </jet-dropdown-link>

      <div class="border-t border-gray-100"></div>

      <!-- Authentication -->
      <form @submit.prevent="logout">
        <jet-dropdown-link as="button"> Log Out </jet-dropdown-link>
      </form>
    </template>
  </jet-dropdown>
</template>

<script>
import { defineComponent } from "vue";
import JetDropdown from "@/Jetstream/Dropdown";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default defineComponent({
  components: {
    JetDropdown,
    JetDropdownLink,
  },
});
</script>
