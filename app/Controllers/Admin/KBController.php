<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class KBController extends BaseController
{
    /**
     * index
     * Hydrates the Knowledge Base Manager (index.php)
     */
    public function index()
    {
        $db = \Config\Database::connect(); //
        
        $data = [
            'userRole'  => session()->get('role'), //
            'userEmail' => session()->get('email'), //
            'title'     => 'Bot Knowledge Manager' //
        ];

        // Fetch categories for the selection menu
        $data['categories'] = $db->table('kb_categories')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray(); //
        
        // Fetch existing articles for the management table
        $data['articles'] = $db->table('kb_articles')
            ->select('kb_articles.*, kb_categories.name as category_name')
            ->join('kb_categories', 'kb_categories.id = kb_articles.category_id', 'left')
            ->orderBy('kb_articles.created_at', 'DESC')
            ->get()->getResultArray(); //

        return view('admin/kb/index', array_merge($this->viewData, $data)); //
    }

    /**
     * storeCategory
     * Adds a new folder category for guides
     */
    public function storeCategory()
    {
        $db = \Config\Database::connect(); //
        $db->table('kb_categories')->insert([
            'name'       => $this->request->getPost('name'), //
            'icon'       => $this->request->getPost('icon') ?: 'fas fa-folder', //
            'created_at' => date('Y-m-d H:i:s') //
        ]);
        
        return redirect()->back()->with('success', 'New category successfully added!'); //
    }

    /**
     * store
     * Saves the guide and maps generic [IMAGE:X] tags to unique files
     */
    public function store()
    {
        $db = \Config\Database::connect(); //
        $answer = $this->request->getPost('answer'); //
        
        // Clean discriminator keywords (e.g., Add_Early_Cutoff) for bot awareness
        $rawKeywords = $this->request->getPost('keywords'); //
        $cleanKeywords = implode(',', array_map('trim', explode(',', $rawKeywords))); //

        // Process Multi-Image Upload
        $files = $this->request->getFiles(); //
        if (isset($files['kb_images'])) {
            foreach ($files['kb_images'] as $index => $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName(); //
                    $img->move(FCPATH . 'assets/img/kb/', $newName); //
                    
                    // Convert user-typed [IMAGE:1] to [IMG_FILE:random_name.png]
                    $placeholder = "[IMAGE:" . ($index + 1) . "]"; //
                    $answer = str_replace($placeholder, "[IMG_FILE:{$newName}]", $answer); //
                }
            }
        }

        $db->table('kb_articles')->insert([
            'category_id' => $this->request->getPost('category_id'), //
            'question'    => $this->request->getPost('question'), //
            'answer'      => $answer, //
            'keywords'    => $cleanKeywords, //
            'created_at'  => date('Y-m-d H:i:s') //
        ]);

        return redirect()->back()->with('success', 'Bot brain updated!'); //
    }

    /**
     * delete
     * Removes the guide and its associated physical images
     */
    public function delete($id)
    {
        $db = \Config\Database::connect(); //
        $article = $db->table('kb_articles')->where('id', $id)->get()->getRowArray(); //

        if ($article) {
            // Find [IMG_FILE:...] tags and delete files from server
            if (preg_match_all('/\[IMG_FILE:(.*?)\]/', $article['answer'], $matches)) { //
                foreach ($matches[1] as $filename) {
                    $path = FCPATH . 'assets/img/kb/' . $filename; //
                    if (file_exists($path)) {
                        unlink($path); //
                    }
                }
            }
            $db->table('kb_articles')->where('id', $id)->delete(); //
        }

        return redirect()->back()->with('success', 'Guide and associated images removed.'); //
    }
}