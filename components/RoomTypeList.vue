<template>
  <div>
    <v-toolbar dense class="elevation-1">
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('roomType_list')" to="/room-types" small flat slot="activator" color="primary">
          All
        </v-btn>
        <span>All Room Type</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn v-if="permissions.includes('roomType_listTrashed')" to="/room-types/trashed" small flat slot="activator" color="error">
          Trashed
        </v-btn>
        <span>Trashed Room Type</span>
      </v-tooltip>
      <v-spacer />
      <RoomTypeAddForm v-if="permissions.includes('roomType_create')" @refresh="fetch"/>
      <v-tooltip bottom>
        <v-btn
          @click.stop.prevent="restoreAll"
          v-if="trashed && permissions.includes('roomType_restoreAll')" icon flat slot="activator">
          <v-icon>reply_all</v-icon>
        </v-btn>
        <span>Restore All Room Type</span>
      </v-tooltip>
      <v-tooltip bottom>
        <v-btn @click.prevent.stop="forceDestroyAll" v-if="trashed && permissions.includes('roomType_forceDestroyAll')" icon flat slot="activator">
          <v-icon>delete</v-icon>
        </v-btn>
        <span>Trash All Room Type</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      :loading="loading"
      :headers="headers"
      :items="roomTypes"
    >
      <template v-slot:items="{item}">
        <td>{{ item.name }}</td>
        <td>{{ item.room }}</td>
        <td align="right">
          <RoomTypeAddForm :room-type="item" v-if="!trashed && permissions.includes('roomType_update')" @refresh="fetch"/>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('roomType_destroy')"
              @click.prevent.stop="() => destroy(item.id)"
              icon flat title="Destroy Room Type">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Destroy Room Type</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="!trashed && permissions.includes('roomType_detach')"
              @click.prevent.stop="() => detach(item.id)"
              icon flat title="Destroy Room Type">
              <v-icon>remove</v-icon>
            </v-btn>
            <span>Detach Room Type</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('roomType_restore')"
              @click.prevent.stop="() => restore(item.id)"
              icon flat title="Restore Room Type">
              <v-icon>reply</v-icon>
            </v-btn>
            <span>Restore Room Type</span>
          </v-tooltip>
          <v-tooltip bottom>
            <v-btn
              slot="activator"
              v-if="trashed && permissions.includes('roomType_forceDestroy')"
              @click.prevent.stop="() => forceDestroy(item.id)"
              icon flat title="Force Destroy Room Type">
              <v-icon>clear</v-icon>
            </v-btn>
            <span>Force Destroy Room Type</span>
          </v-tooltip>
        </td>

      </template>
    </v-data-table>
  </div>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoomTypeAddForm from "./RoomTypeAddForm";
  export default {
    name: "RoomTypesList",
    components: {RoomTypeAddForm},
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
      roomTypes: [],
      loading: false,
      headers: [
        {
          text: 'Name',
          align: 'left',
          sortable: false,
          value: 'name'
        },
        {
          text: 'Total',
          align: 'left',
          sortable: false,
          value: 'name'
        },
        {
          sortable: false,
        }
      ]
    }),
    methods: {
      detach(id) {
        this.$confirm('Are you sure you want to detach the room type?')
          .then(async e => {
            if(e === false) return null;
            await this.$axios.$patch('/room-types/detach/' + id)
            await this.fetch();
          })
      },
      destroy(id) {
        this.$confirm('Are you sure you want to delete the room type?')
          .then(async e => {
            if(e === false) return null;
            await this.$axios.$delete('/room-types/' + id)
            this.$router.push('/room-types/trashed')
          })
      },
      restore(id) {
        this.$confirm('Are you sure you want to bring the roomType back?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/room-types/restore/' + id)
            this.$router.push('/roomTypes')
          })
      },
      forceDestroy(id) {
        this.$confirm('Are you sure you want to permanently delete the room type?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/room-types/force/' + id)
            this.$router.push('/roomTypes')
          })
      },
      restoreAll() {
        this.$confirm('Are you sure you want to recover the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$patch('/room-types/restore/all')
            this.$router.push('/roomTypes')
          })
      },
      forceDestroyAll() {
        this.$confirm('Are you sure you want to clean the trash?')
          .then(async (e) => {
            if(e === false) return null
            await this.$axios.$delete('/room-types/force/all')
            this.$router.push('/roomTypes')
          })
      },
      async fetch() {
        try {
          this.loading = true;
          let url = '/room-types'
          if(this.trashed) {
            url += "?only=trashed"
          }
          const {data:{results}} = await this.$axios.get(url)
          this.roomTypes = results;
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
