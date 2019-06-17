<template>
  <div>
    <v-toolbar dense class="elevation-1">
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('room_list')" to="/rooms" small flat slot="activator" color="primary">
          All
        </v-btn>
        <span>All Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('room_listTrashed')" to="/rooms/trashed" small flat slot="activator" color="error">
          Trashed
        </v-btn>
        <span>Trashed Hotel</span>
      </v-tooltip>
      <v-spacer />
      <v-tooltip v-if="trashed === false" bottom>
        <v-btn v-if="permissions.includes('room_create')" to="/rooms/add" icon flat slot="activator">
          <v-icon>add</v-icon>
        </v-btn>
        <span>Add Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn
          @click.stop.prevent="restoreAll"
          v-if="trashed && permissions.includes('room_restoreAll')" icon flat slot="activator">
          <v-icon>reply_all</v-icon>
        </v-btn>
        <span>Restore All Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn @click.prevent.stop="forceDestroyAll" v-if="trashed && permissions.includes('room_forceDestroyAll')" icon flat slot="activator">
          <v-icon>delete</v-icon>
        </v-btn>
        <span>Trash All Hotel</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      :loading="loading"
      :headers="headers"
      :items="rooms"
    >
      <template v-slot:items="{item}">
        <td>
          <v-img :src="item.image" />
        </td>
        <td>{{ item.name }}</td>
        <td>{{ item.type.name }}</td>
        <td>{{ item.capacity.name }}</td>
        <td align="right">
          <v-tooltip bottom>
            <v-btn
              v-if="!trashed && permissions.includes('room_update')"
              icon flat
              :to="`/rooms/edit/${item.id}`"
              slot="activator"
              title="Edit Hotel">
              <v-icon>edit</v-icon>
            </v-btn>
            <span>Edit Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('room_destroy')"
              @click.prevent.stop="() => destroy(item.id)"
              icon flat title="Destroy Hotel">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Destroy Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('room_restore')"
              @click.prevent.stop="() => restore(item.id)"
              icon flat title="Restore Hotel">
              <v-icon>reply</v-icon>
            </v-btn>
            <span>Restore Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('room_forceDestroy')"
              @click.prevent.stop="() => forceDestroy(item.id)"
              icon flat title="Force Destroy Hotel">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Force Destroy Hotel</span>
          </v-tooltip>
        </td>

      </template>
    </v-data-table>
  </div>
</template>

<script>
  import {mapGetters} from "vuex"
  export default {
    name: "HotelList",
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
      rooms: [],
      loading: false,
      headers: [
        {
          sortable: false,
        },
        {
          text: 'Name',
          align: 'left',
          sortable: false,
        },
        {
          text: 'Type',
          align: 'left',
          sortable: false,
        },
        {
          text: 'Capacity',
          align: 'left',
          sortable: false,
        },
        {
          sortable: false,
        }
      ]
    }),
    methods: {
      destroy(id) {
        this.$confirm('Are you sure you want to delete the room?')
          .then(async () => {
            await this.$axios.$delete('/rooms/' + id)
            this.$router.push('/rooms/trashed')
          })
      },
      restore(id) {
        this.$confirm('Are you sure you want to bring the room back?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/rooms/restore/' + id)
            this.$router.push('/rooms')
          })
      },
      forceDestroy(id) {
        this.$confirm('Are you sure you want to permanently delete the room?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/rooms/force/' + id)
            this.$router.push('/rooms')
          })
      },
      restoreAll() {
        this.$confirm('Are you sure you want to recover the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/rooms/restore/all')
            this.$router.push('/rooms')
          })
      },
      forceDestroyAll() {
        this.$confirm('Are you sure you want to clean the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/rooms/force/all')
            this.$router.push('/rooms')
          })
      },
      async fetch() {
        try {
          this.loading = true;
          let url = '/rooms'
          if(this.trashed) {
            url += "?only=trashed"
          }
          const {data:{results}} = await this.$axios.get(url)
          this.rooms = results;
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
