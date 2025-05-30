<?php

class Image extends CI_Controller
{
    public function resize($size)
{
    // Get image relative path from query param
    $relativePath = isset($_GET['img']) ? $_GET['img'] : '';
    if (!$relativePath) {
        show_404('Image path not provided.');
    }

    $originalPath = FCPATH . 'uploads/' . $relativePath;
    $cachePath = FCPATH . 'cache/' . $size . '_' . $relativePath;

    // Check if original image exists
    if (!file_exists($originalPath)) {
        show_404('Original image not found.');
    }

    // ✅ Ensure cache directory exists
    $cacheDir = dirname($cachePath);
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true); // recursive folder create
    }

    // ✅ Serve from cache if exists
    if (file_exists($cachePath)) {
        return $this->output
            ->set_content_type(get_mime_by_extension($cachePath))
            ->set_output(file_get_contents($cachePath));
    }

    // ✅ Extract width & height
    list($width, $height) = explode('x', $size);

    $this->load->library('image_lib');

    // Resize config
    $config = [
        'image_library'  => 'gd2',
        'source_image'   => $originalPath,
        'new_image'      => $cachePath,
        'maintain_ratio' => TRUE,
        'width'          => $width,
        'height'         => $height,
    ];

    $this->image_lib->initialize($config);

    // ✅ Resize image
    if (!$this->image_lib->resize()) {
        show_error($this->image_lib->display_errors());
    }

    // ✅ Output resized image
    return $this->output
        ->set_content_type(get_mime_by_extension($cachePath))
        ->set_output(file_get_contents($cachePath));
}

}

?>