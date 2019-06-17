<template>
  <div id="upload" @click="open">
    <v-icon v-if="preview === null">store</v-icon>
    <v-img :src="preview" v-else/>
    <input type="file" :accept="accept.join(',')" @change="file">
  </div>
</template>

<script>
  export default {
    name: "Upload",
    props: {
      accept: {
        type: Array,
        default: () => ([])
      },
      value: {
        type: [String, File]
      }
    },
    data: () => ({
      preview: null
    }),
    watch: {
      value(e) {
        if (!!e && typeof e === "string" && this.preview === null) {
          this.preview = e
        } else if (!!e && typeof e === "object" && this.preview === null) {
          this.read(e)
        }
      }
    },
    methods: {
      file({target: {files}}) {
        const file = files[0]
        if (file === undefined) {
          return null
        }
        if (this.accept.includes(file.type) === false) {
          this.$el.querySelector('input[type=file]').value = "";
          this.$confirm('The selected file format is incorrect, you do not want to try again?')
            .then((e) => {
              if (e !== false) {
                this.$el.querySelector('input[type=file]').click()
              }
            })
          return;
        }
        this.read(file)
      },
      read(file) {
        const form = new FileReader();
        form.onload = ({target: {result}}) => this.preview = result
        form.readAsDataURL(file)
        this.$emit('input', file)
      },
      open() {
        this.preview = null;
        this.$emit('input', null);
        this.$el.querySelector('input[type=file]').click()
      }
    }
  }
</script>

<style scoped>
  #upload {
    width: 250px;
    height: 250px;
    display: flex;
    justify-content: center;
    align-content: center;
    background-color: rgba(0, 0, 0, .015);
    border: 1px solid rgba(0, 0, 0, .025);
    margin: 0 auto;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  #upload:hover, #upload:focus {
    background-color: rgba(0, 0, 0, .035);
    border: 1px solid rgba(0, 0, 0, .045);
  }

  #upload .v-icon {
    font-size: 8rem;
  }

  #upload input[type=file] {
    visibility: hidden;
    display: none;
    opacity: 0;
    z-index: -1;
    position: absolute;
  }
</style>
