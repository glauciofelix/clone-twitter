<?php
    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model{
        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            return $this->$atributo = $valor;
        }

        // Salvar
        public function salvar(){
            
            $query = "INSERT INTO tweets(id_usuario, tweet)VALUES(:id_usuario, :tweet)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));
            $stmt->execute();

            return $this;
        }

        // Recuperar
        public function getAll(){
            
            $query = "SELECT 
                        t.id, 
                        t.id_usuario, 
                        u.nome, 
                        t.tweet, 
                        DATE_FORMAT(t.DATA, '%d/%m/%Y %H:%i') as data
                    FROM 
                        tweets AS t LEFT JOIN usuarios AS u ON (t.id_usuario = u.id)
                    WHERE 
                        t.id_usuario = :id_usuario
                        OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores
                            WHERE id_usuario = :id_usuario)
                    ORDER BY
                        t.data DESC
                    ";
                

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // recuperar com paginação
        public function getPorPagina($limit, $offset){
            
            $query = "SELECT 
                        t.id, 
                        t.id_usuario, 
                        u.nome, 
                        t.tweet, 
                        DATE_FORMAT(t.DATA, '%d/%m/%Y %H:%i') as data
                    FROM 
                        tweets AS t LEFT JOIN usuarios AS u ON (t.id_usuario = u.id)
                    WHERE 
                        t.id_usuario = :id_usuario
                        OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores
                            WHERE id_usuario = :id_usuario)
                    ORDER BY
                        t.data DESC
                    LIMIT 
                        $limit
                    OFFSET
                        $offset
                    ";
                

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        // recuperar total tweets
        public function getTotalRegistros(){
            
            $query = "SELECT 
                        count(*) AS total
                    FROM 
                        tweets AS t LEFT JOIN usuarios AS u ON (t.id_usuario = u.id)
                    WHERE 
                        t.id_usuario = :id_usuario
                        OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores
                            WHERE id_usuario = :id_usuario)
                    ";
                

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        
    }