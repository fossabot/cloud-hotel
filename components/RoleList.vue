<template>
  <div>
    <v-toolbar dense class="elevation-1">
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('role_list')" to="/roles" small flat slot="activator" color="primary">
          All
        </v-btn>
        <span>All User</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('role_listTrashed')" to="/roles/trashed" small flat slot="activator" color="error">
          Trashed
        </v-btn>
        <span>Trashed User</span>
      </v-tooltip>
      <v-spacer />
      <v-tooltip v-if="trashed === false" bottom>
        <v-btn v-if="permissions.includes('role_create')" to="/roles/add" icon flat slot="activator">
          <v-icon>add</v-icon>
        </v-btn>
        <span>Add Role</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn
          @click.stop.prevent="restoreAll"
          v-if="trashed && permissions.includes('role_restoreAll')" icon flat slot="activator">
          <v-icon>reply_all</v-icon>
        </v-btn>
        <span>Restore All Role</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn @click.prevent.stop="forceDestroyAll" v-if="trashed && permissions.includes('role_forceDestroyAll')" icon flat slot="activator">
          <v-icon>delete</v-icon>
        </v-btn>
        <span>Trash All Role</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table :loading="loading" :headers="headers" :items="roles">
      <template v-slot:items="{item}">
        <td>{{ item.name }}</td>
        <td>{{ item.permissions && item.permissions.join(', ')}}</td>
        <td align="right">
          <v-tooltip bottom>
            <v-btn
              v-if="!trashed && permissions.includes('role_update') && item.name !== 'User'"
              icon flat
              :to="`/roles/edit/${item.id}`"
              slot="activator"
              title="Edit User">
              <v-icon>edit</v-icon>
            </v-btn>
            <span>Edit User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('role_destroy') && item.name !== 'User'"
              @click.prevent.stop="() => destroy(item.id)"
              icon flat title="Destroy User">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Destroy User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('role_restore') && item.name !== 'User'"
              @click.prevent.stop="() => restore(item.id)"
              icon flat title="Restore User">
              <v-icon>reply</v-icon>
            </v-btn>
            <span>Restore User</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('role_forceDestroy') && item.name !== 'User'"
              @click.prevent.stop="() => forceDestroy(item.id)"
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
    name: "RoleList",
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
      roles: [],
      loading: false,
      headers: [
        {
          text: 'Name',
          align: 'left',
          sortable: false,
          value: 'name'
        },
        {
          text: 'Permissions',
          align: 'left',
          sortable: false,
          value: 'permissions'
        },
        {
          sortable: false,
        }
      ]
    }),
    methods: {
      destroy(id) {
        this.$confirm('Are you sure you want to delete the role?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/roles/' + id)
            this.$router.push('/roles/trashed')
          })
      },
      restore(id) {
        this.$confirm('Are you sure you want to bring the role back?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/roles/restore/' + id)
            this.$router.push('/roles')
          })
      },
      forceDestroy(id) {
        this.$confirm('Are you sure you want to permanently delete the role?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/roles/force/' + id)
            this.$router.push('/roles')
          })
      },
      restoreAll() {
        this.$confirm('Are you sure you want to recover the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/roles/restore/all')
            this.$router.push('/roles')
          })
      },
      forceDestroyAll() {
        this.$confirm('Are you sure you want to clean the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/roles/force/all')
            this.$router.push('/roles')
          })
      },
      async fetch() {
        try {
          this.loading = true;
          let url = '/roles'
          if(this.trashed) {
            url += "?only=trashed"
          }
          const {data:{results}} = await this.$axios.get(url)
          this.roles = results;
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
