<template>
  <v-select
    :loading="loading"
    v-bind="$attrs"
    :items="roomTypes"
    item-text="name"
    item-value="id"
    @change="e => $emit('input', e)"
  />
</template>

<script>
  export default {
    name: "RoomTypeSelect",
    data: () => ({
      roomTypes: [],
      loading: false,
    }),
    async created() {
      try {
        this.loading = true;
        let url = '/room-types?list'
        const {data:{results}} = await this.$axios.get(url);
        this.roomTypes = results;
      } finally {
        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>
