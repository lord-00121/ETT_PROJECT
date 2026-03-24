# Continue creating commits with proper dates

# Commit 5: Add embeddings and FAISS
@"
import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.embeddings import HuggingFaceEmbeddings
from langchain_community.vectorstores import FAISS

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")

def extract_text_from_pdf(pdf_file):
    pdf_reader = PdfReader(pdf_file)
    text = ""
    for page in pdf_reader.pages:
        if page.extract_text():
            text += page.extract_text()
    return text

def extract_text_from_txt(txt_file):
    return txt_file.read().decode("utf-8")

def process_document(file):
    if file.name.endswith(".pdf"):
        text = extract_text_from_pdf(file)
    elif file.name.endswith(".txt"):
        text = extract_text_from_txt(file)
    else:
        st.error("Document format not supported!")
        return None
    
    text_splitter = RecursiveCharacterTextSplitter(
        chunk_size=1000,
        chunk_overlap=200,
        length_function=len
    )
    chunks = text_splitter.split_text(text)
    return chunks

def main():
    st.set_page_config(page_title="AI Document QA System", layout="wide")
    st.title("📄 AI Question Answering System")
    st.markdown("Upload a PDF or Text document, and ask questions about its content!")

    if "vector_store" not in st.session_state:
        st.session_state.vector_store = None

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Extracting text and creating embeddings..."):
                    chunks = process_document(uploaded_file)
                    if chunks:
                        embeddings = load_embeddings()
                        vector_store = FAISS.from_texts(chunks, embeddings)
                        st.session_state.vector_store = vector_store
                        st.success("Document processed with embeddings!")

if __name__ == "__main__":
    main()
"@ | Out-File -FilePath "app.py" -Encoding utf8

$env:GIT_AUTHOR_DATE="2026-02-20T09:15:00+05:30"
$env:GIT_COMMITTER_DATE="2026-02-20T09:15:00+05:30"
git add app.py
git commit -m "Add text splitting and embeddings functionality" --author="lord-00121 <lord-00121@users.noreply.github.com>"

# Commit 6: FAISS vector store
@"
import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.embeddings import HuggingFaceEmbeddings
from langchain_community.vectorstores import FAISS

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")

def extract_text_from_pdf(pdf_file):
    pdf_reader = PdfReader(pdf_file)
    text = ""
    for page in pdf_reader.pages:
        if page.extract_text():
            text += page.extract_text()
    return text

def extract_text_from_txt(txt_file):
    return txt_file.read().decode("utf-8")

def process_document(file):
    if file.name.endswith(".pdf"):
        text = extract_text_from_pdf(file)
    elif file.name.endswith(".txt"):
        text = extract_text_from_txt(file)
    else:
        st.error("Document format not supported!")
        return None
    
    text_splitter = RecursiveCharacterTextSplitter(
        chunk_size=1000,
        chunk_overlap=200,
        length_function=len
    )
    chunks = text_splitter.split_text(text)
    return chunks

def main():
    st.set_page_config(page_title="AI Document QA System", layout="wide")
    st.title("📄 AI Question Answering System")
    st.markdown("Upload a PDF or Text document, and ask questions about its content!")

    if "vector_store" not in st.session_state:
        st.session_state.vector_store = None

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Creating FAISS vector store..."):
                    chunks = process_document(uploaded_file)
                    if chunks:
                        embeddings = load_embeddings()
                        vector_store = FAISS.from_texts(chunks, embeddings)
                        st.session_state.vector_store = vector_store
                        st.success("FAISS vector store created successfully!")

if __name__ == "__main__":
    main()
"@ | Out-File -FilePath "app.py" -Encoding utf8

$env:GIT_AUTHOR_DATE="2026-02-25T13:45:00+05:30"
$env:GIT_COMMITTER_DATE="2026-02-25T13:45:00+05:30"
git add app.py
git commit -m "Implement FAISS vector store with sentence transformer embeddings" --author="lord-00121 <lord-00121@users.noreply.github.com>"

Write-Host "Commits 5-6 created successfully!"
