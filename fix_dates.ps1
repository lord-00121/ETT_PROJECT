# Create commits with proper dates using environment variables

# Commit 2: Requirements
@"
streamlit
PyPDF2
langchain
langchain-community
sentence-transformers
transformers
faiss-cpu
torch
accelerate
"@ | Out-File -FilePath "requirements.txt" -Encoding utf8

$env:GIT_AUTHOR_DATE="2026-02-08T14:30:00+05:30"
$env:GIT_COMMITTER_DATE="2026-02-08T14:30:00+05:30"
git add requirements.txt
git commit -m "Add project dependencies and requirements" --author="lord-00121 <lord-00121@users.noreply.github.com>"

# Commit 3: Basic Streamlit app
@"
import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return None

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
    
    return text

def main():
    st.set_page_config(page_title="AI Document QA System", layout="wide")
    st.title("📄 AI Question Answering System")
    st.markdown("Upload a PDF or Text document, and ask questions about its content!")

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Extracting text..."):
                    text = process_document(uploaded_file)
                    if text:
                        st.session_state.processed_text = text
                        st.success("Document processed successfully!")

if __name__ == "__main__":
    main()
"@ | Out-File -FilePath "app.py" -Encoding utf8

$env:GIT_AUTHOR_DATE="2026-02-12T16:45:00+05:30"
$env:GIT_COMMITTER_DATE="2026-02-12T16:45:00+05:30"
git add app.py
git commit -m "Add Streamlit application skeleton with basic UI structure" --author="lord-00121 <lord-00121@users.noreply.github.com>"

# Commit 4: Add text splitting
@"
import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader
from langchain_text_splitters import RecursiveCharacterTextSplitter

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return None

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

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Extracting text and creating chunks..."):
                    chunks = process_document(uploaded_file)
                    if chunks:
                        st.session_state.chunks = chunks
                        st.success(f"Document processed successfully! Created {len(chunks)} chunks.")

if __name__ == "__main__":
    main()
"@ | Out-File -FilePath "app.py" -Encoding utf8

$env:GIT_AUTHOR_DATE="2026-02-15T11:20:00+05:30"
$env:GIT_COMMITTER_DATE="2026-02-15T11:20:00+05:30"
git add app.py
git commit -m "Implement PDF and TXT document processing functionality" --author="lord-00121 <lord-00121@users.noreply.github.com>"

Write-Host "First 4 commits created successfully!"
