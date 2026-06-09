<?php

class Node
{
        public int $id;
        
        public int $user_id;

        public string $name;

        public bool $isFolder;

        public int $parentId;

        public string $storagePath; 

        public float $size; 

        public int $createdAt;


        public static function fromDbRow(array $row): self
        {
            $node = new self();
            $node->id = $row["id"];
            $node->user_id = $row["user_id"];
            $node->name = $row["name"];
            $node->isFolder = $row["is_folder"];
            $node->parentId = $row["parent_id"];
            $node->storagePath = $row["storage_path"];
            $node->size = $row["size"];
            $node->createdAt = $row["created_at"];

            return $node;
        }
}
?>