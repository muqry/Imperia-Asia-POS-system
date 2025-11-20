window.addEventListener("closeModal", (event) => {
    $(".closeModel").modal("hide");
    $(".modal-backdrop").remove(); //tutup backdrop
});

//Success Message Dialog

window.addEventListener("MSGSuccessfully", (event) => {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: "success",
        title: event.detail.title //?? "Section Inserted successfully!", //toast message yang ni
    });
});

//Delete Section

window.addEventListener("Swal:DeletedRecord", (event) => {
    console.log(event.detail);

    Swal.fire({
        title: event.detail.title,
        html: event.detail.text,  // use html so <span> works
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('RecordDeleted', { section_id: event.detail.id })
            Swal.fire({
                title: "Deleted!",
                text: "Record Deleted Successfully!",
                icon: "success",
            });
        }
    });
});
