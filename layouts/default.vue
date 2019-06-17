<template>
  <div>
    <v-app v-if="show && permission.length > 0">
      <v-navigation-drawer
        v-model="drawer"
        :mini-variant="miniVariant"
        clipped
        fixed
        app
      >
        <v-list>
          <v-list-tile
            v-for="(item, i) in items"
            v-if="permission.includes(item.permission)"
            :key="i"
            :to="item.to"
            router
            exact
          >
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title v-text="item.title"/>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-navigation-drawer>
      <v-toolbar clipped-left fixed app>
        <v-toolbar-side-icon @click="drawer = !drawer"/>
        <v-btn :disabled="!drawer" icon @click.stop="miniVariant = !miniVariant">
          <v-icon>{{ `chevron_${miniVariant ? 'right' : 'left'}` }}</v-icon>
        </v-btn>
        <v-toolbar-title v-text="me.hotels && me.hotels.name || 'Cloud Hotel'"/>
        <v-spacer/>
        <v-btn @click="logout" icon flat>
          <v-icon>power_settings_new</v-icon>
        </v-btn>
      </v-toolbar>
      <v-content>
        <v-container>
          <nuxt/>
        </v-container>
      </v-content>

      <v-footer fixed app>
        <span>&copy; 2019</span>
      </v-footer>
    </v-app>
    <v-app v-else>
      Loading
    </v-app>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'

  export default {
    computed: {
      ...mapGetters({
        permission: "init/PERMISSIONS",
        show: "init/SHOW_PAGE",
        me: "init/ME"
      })
    },
    data: () => ({
      drawer: true,
      miniVariant: false,
      items: [
        {
          permission: 'dashboard_show',
          icon: 'apps',
          title: 'Dashboard',
          to: '/',
        },
        {
          icon: 'person',
          title: 'List Users',
          permission: 'user_list',
          to: '/users'
        },
        {
          icon: 'security',
          title: 'List Roles',
          permission: 'role_list',
          to: '/roles'
        },
        {
          icon: 'store',
          title: 'List Hotels',
          permission: 'hotel_list',
          to: '/hotels'
        },
        {
          icon: 'meeting_room',
          title: 'List Rooms',
          permission: 'room_list',
          to: '/rooms'
        },
        {
          icon: 'meeting_room',
          title: 'List Room Types',
          permission: 'roomType_list',
          to: '/room-types'
        },
        {
          icon: 'meeting_room',
          title: 'List Room Capacity',
          permission: 'roomCapacity_list',
          to: '/room-capacities'
        }
      ]
    }),
    methods: {
      logout() {
        this.$cookie.remove('token', {path: '/'})
        this.$router.push('/login')
        this.$toast.info("Logout successful")
      }
    }
  }
</script>
