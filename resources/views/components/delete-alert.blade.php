@props(['route', 'itemName' => 'item', 'deleteBtnClass' => 'delete-btn']) {{-- item/ the default value if I did not pass the itemName --}}

<script>
    document.querySelectorAll('.{{ $deleteBtnClass }}').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete the {{ $itemName }}: <strong>${name}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = "{{ route($route, '') }}/" + itemId;
                    form.submit();
                }
            });
        });
    });
</script>
