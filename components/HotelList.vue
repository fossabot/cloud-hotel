<template>
  <div>
    <v-toolbar dense class="elevation-1">
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('hotel_list')" to="/hotels" small flat slot="activator" color="primary">
          All
        </v-btn>
        <span>All Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('hotel_listTrashed')" to="/hotels/trashed" small flat slot="activator" color="error">
          Trashed
        </v-btn>
        <span>Trashed Hotel</span>
      </v-tooltip>
      <v-spacer />
      <v-tooltip v-if="trashed === false" bottom>
        <v-btn v-if="permissions.includes('hotel_create')" to="/hotels/add" icon flat slot="activator">
          <v-icon>add</v-icon>
        </v-btn>
        <span>Add Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn
          @click.stop.prevent="restoreAll"
          v-if="trashed && permissions.includes('hotel_restoreAll')" icon flat slot="activator">
          <v-icon>reply_all</v-icon>
        </v-btn>
        <span>Restore All Hotel</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn @click.prevent.stop="forceDestroyAll" v-if="trashed && permissions.includes('hotel_forceDestroyAll')" icon flat slot="activator">
          <v-icon>delete</v-icon>
        </v-btn>
        <span>Trash All Hotel</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      :loading="loading"
      :headers="headers"
      :items="hotels"
    >
      <template v-slot:items="{item}">
        <td>
          <v-img :src="item.image" />
        </td>
        <td>{{ item.name }}</td>
        <td>{{ item.email }}</td>
        <td>{{ item.metas.city }}</td>
        <td>{{ item.metas.country }}</td>
        <td align="right">
          <v-tooltip bottom>
            <v-btn
              v-if="!trashed && permissions.includes('hotel_update')"
              icon flat
              :to="`/hotels/edit/${item.id}`"
              slot="activator"
              title="Edit Hotel">
              <v-icon>edit</v-icon>
            </v-btn>
            <span>Edit Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('hotel_destroy')"
              @click.prevent.stop="() => destroy(item.id)"
              icon flat title="Destroy Hotel">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Destroy Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('hotel_restore')"
              @click.prevent.stop="() => restore(item.id)"
              icon flat title="Restore Hotel">
              <v-icon>reply</v-icon>
            </v-btn>
            <span>Restore Hotel</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('hotel_forceDestroy')"
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
      hotels: [],
      loading: false,
      headers: [
        {
          sortable: false,
        },
        {
          text: 'Name',
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
          text: 'City',
          align: 'left',
          sortable: false,
          value: 'role'
        },
        {
          text: 'Country',
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
        this.$confirm('Are you sure you want to delete the hotel?')
          .then(async () => {
            await this.$axios.$delete('/hotels/' + id)
            this.$router.push('/hotels/trashed')
          })
      },
      restore(id) {
        this.$confirm('Are you sure you want to bring the hotel back?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/hotels/restore/' + id)
            this.$router.push('/hotels')
          })
      },
      forceDestroy(id) {
        this.$confirm('Are you sure you want to permanently delete the hotel?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/hotels/force/' + id)
            this.$router.push('/hotels')
          })
      },
      restoreAll() {
        this.$confirm('Are you sure you want to recover the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/hotels/restore/all')
            this.$router.push('/hotels')
          })
      },
      forceDestroyAll() {
        this.$confirm('Are you sure you want to clean the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/hotels/force/all')
            this.$router.push('/hotels')
          })
      },
      async fetch() {
        try {
          this.loading = true;
          let url = '/hotels'
          if(this.trashed) {
            url += "?only=trashed"
          }
          const {data:{results}} = await this.$axios.get(url)
          this.hotels = results;
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
