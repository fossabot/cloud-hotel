<template>
  <div>
    <v-toolbar dense class="elevation-1">
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('user_list')" to="/users" small flat slot="activator" color="primary">
          All
        </v-btn>
        <span>All User</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('user_listTrashed')" to="/users/trashed" small flat slot="activator" color="error">
          Trashed
        </v-btn>
        <span>Trashed User</span>
      </v-tooltip>
      <v-spacer />
      <v-tooltip v-if="trashed === false" bottom>
        <v-btn v-if="permissions.includes('user_create')" to="/users/add" icon flat slot="activator">
          <v-icon>add</v-icon>
        </v-btn>
        <span>Add User</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn
          @click.stop.prevent="restoreAll"
          v-if="trashed && permissions.includes('user_restoreAll')" icon flat slot="activator">
          <v-icon>reply_all</v-icon>
        </v-btn>
        <span>Restore All User</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn @click.prevent.stop="forceDestroyAll" v-if="trashed && permissions.includes('user_forceDestroyAll')" icon flat slot="activator">
          <v-icon>delete</v-icon>
        </v-btn>
        <span>Trash All User</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      :loading="loading"
      :headers="headers"
      :items="users"
    >
      <template v-slot:items="{item:{id, displayName, roles, email}}">
        <td>{{ displayName }}</td>
        <td>{{ email }}</td>
        <td>{{ roles.name }}</td>
        <td align="right">
          <v-tooltip bottom>
            <v-btn
              v-if="!trashed && permissions.includes('user_update')"
              icon flat
              :to="`/users/edit/${id}`"
              slot="activator"
              title="Edit User">
              <v-icon>edit</v-icon>
            </v-btn>
            <span>Edit User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('user_destroy')"
              @click.prevent.stop="() => destroy(id)"
              icon flat title="Destroy User">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Destroy User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('user_restore')"
              @click.prevent.stop="() => restore(id)"
              icon flat title="Restore User">
              <v-icon>reply</v-icon>
            </v-btn>
            <span>Restore User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('user_forceDestroy')"
              @click.prevent.stop="() => forceDestroy(id)"
              icon flat title="Force Destroy User">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Force Destroy User</span>
          </v-tooltip>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import {mapGetters} from "vuex"
  export default {
    name: "UserList",
    props: {
      trashed: {
        type: Boolean,
        default: () => false
      }
    },
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    data: () => ({
      users: [],
      loading: false,
      headers: [
        {
          text: 'Display Name',
          align: 'left',
          sortable: false,
          value: 'displayName'
        },
        {
          text: 'Email Address',
          align: 'left',
          sortable: false,
          value: 'email'
        },
        {
          text: 'Role',
          align: 'left',
          sortable: false,
          value: 'role'
        },
        {
          sortable: false,
        }
      ]
    }),
    methods: {
      destroy(id) {
        this.$confirm('Are you sure you want to delete the user?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/users/' + id)
            this.$router.push('/users/trashed')
          })
      },
      restore(id) {
        this.$confirm('Are you sure you want to bring the user back?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/users/restore/' + id)
            this.$router.push('/users')
          })
      },
      forceDestroy(id) {
        this.$confirm('Are you sure you want to permanently delete the user?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/users/force/' + id)
            this.$router.push('/users')
          })
      },
      restoreAll() {
        this.$confirm('Are you sure you want to recover the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/users/restore/all')
            this.$router.push('/users')
          })
      },
      forceDestroyAll() {
        this.$confirm('Are you sure you want to clean the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/users/force/all')
            this.$router.push('/users')
          })
      },
      async fetch() {
        try {
          this.loading = true;
          let url = '/users'
          if(this.trashed) {
            url += "?only=trashed"
          }
          const {data:{results}} = await this.$axios.get(url)
          this.users = results;
        } finally {
          this.loading = false
        }

      }
    },
    async created() {
      await this.fetch()
    }
  }
</script>

<style scoped>

</style>
