<template>
<div
      :class="{
        block: showingNavigationDropdown,
        hidden: !showingNavigationDropdown,
      }"
      class="sm:hidden"
    >
      <div class="pt-2 pb-3 space-y-1">
        <jet-responsive-nav-link
          :href="route('dashboard')"
          :active="route().current('dashboard')"
        >
          Dashboard
        </jet-responsive-nav-link>
      </div>

      <!-- Responsive Settings Options -->
      <div class="pt-4 pb-1 border-t border-base-100">
        <div class="flex items-center px-4">
          <div
            v-if="$page.props.jetstream.managesProfilePhotos"
            class="flex-shrink-0 mr-3"
          >
            <img
              class="h-10 w-10 rounded-full object-cover"
              :src="$page.props.user.profile_photo_url"
              :alt="$page.props.user.name"
            />
          </div>

          <div>
            <div class="font-medium text-base ">
              {{ $page.props.user.name }}
            </div>
            <div class="font-medium text-sm ">
              {{ $page.props.user.email }}
            </div>
          </div>
        </div>

        <div class="mt-3 space-y-1">
          <jet-responsive-nav-link
            :href="route('profile.show')"
            :active="route().current('profile.show')"
          >
            Profile
          </jet-responsive-nav-link>

          <jet-responsive-nav-link
            :href="route('api-tokens.index')"
            :active="route().current('api-tokens.index')"
            v-if="$page.props.jetstream.hasApiFeatures"
          >
            API Tokens
          </jet-responsive-nav-link>

          <!-- Authentication -->
          <form method="POST" @submit.prevent="logout">
            <jet-responsive-nav-link as="button">
              Log Out
            </jet-responsive-nav-link>
          </form>

          <!-- Team Management -->
          <template v-if="$page.props.jetstream.hasTeamFeatures">
            <div class="border-t border-base-100"></div>

            <div class="block px-4 py-2 text-xs ">Manage Team</div>

            <!-- Team Settings -->
            <jet-responsive-nav-link
              :href="route('teams.show', $page.props.user.current_team)"
              :active="route().current('teams.show')"
            >
              Team Settings
            </jet-responsive-nav-link>

            <jet-responsive-nav-link
              :href="route('teams.create')"
              :active="route().current('teams.create')"
              v-if="$page.props.jetstream.canCreateTeams"
            >
              Create New Team
            </jet-responsive-nav-link>

            <div class="border-t border-base-100"></div>

            <!-- Extra Features -->
            <!-- @todo - make these dynamic, as some users wont have access -->
            <div class="block px-4 py-2 text-xs ">Extras</div>
            <jet-responsive-nav-link :href="route('workout-generator')">
              Workout Generator
            </jet-responsive-nav-link>
            <jet-responsive-nav-link :href="route('workout-generator')">
              Sales Slideshow
            </jet-responsive-nav-link>

            <div class="border-t border-base-100-100"></div>

            <!-- Team Switcher -->
            <div class="block px-4 py-2 text-xs ">
              Switch Teams
            </div>

            <template v-for="team in $page.props.user.all_teams" :key="team.id">
              <form @submit.prevent="switchToTeam(team)">
                <jet-responsive-nav-link as="button">
                  <div class="flex items-center">
                    <svg
                      v-if="team.id == $page.props.user.current_team_id"
                      class="mr-2 h-5 w-5 text-green-400"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                    <div>{{ team.name }}</div>
                  </div>
                </jet-responsive-nav-link>
              </form>
            </template>
          </template>
        </div>
      </div>
    </div>
    </template>
